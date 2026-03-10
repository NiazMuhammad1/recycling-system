<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Mpdf\Mpdf;

class CollectionPdfController extends Controller
{
    public function dutyOfCare(Collection $collection)
    {
        $collection->load(['items.category', 'user', 'client']);

        // All duty of care categories
        $categories = \App\Models\Category::whereIn('type', ['duty_of_care','both'])->get();

        // Items belonging to this collection
        $items = $collection->items;

        // Prepare rows
        $rows = [];

        foreach ($categories as $cat) {

            $catItems = $items->where('category_id', $cat->id);

            if ($catItems->count()) {

                $qty = $catItems->sum('qty');

                $totalWeight = $catItems->sum(function ($it) {
                    return $it->weight_kg ?? 0;
                });

                $perItem = $qty ? ($totalWeight / $qty) : 0;

                // if renamed category_name exists use it
                $displayName = $catItems->first()->category_name ?: $cat->name;

            } else {

                $qty = 0;
                $totalWeight = 0;
                $perItem = 0;
                $displayName = $cat->name;
            }

            $rows[] = (object)[
                'name' => $displayName,
                'qty' => $qty,
                'total_weight' => $totalWeight,
                'per_item_weight' => $perItem,
                'ewc_code' => $cat->ewc_code,
            ];
        }

        $totalItems = collect($rows)->sum('qty');
        $totalWeight = collect($rows)->sum('total_weight');

        $html = view('pdf.duty_of_care', compact(
            'collection',
            'rows',
            'totalItems',
            'totalWeight'
        ))->render();

        $mpdf = $this->makeMpdf();
        $mpdf->WriteHTML($html);

        return response($mpdf->Output("duty_of_care_{$collection->id}.pdf", 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="duty_of_care_'.$collection->id.'.pdf"',
        ]);
    }

    public function hazardous(Collection $collection)
    {
        $collection->load(['items.category','user','client']);

        // All hazardous categories
        $categories = \App\Models\Category::whereIn('type',['hazard','both'])->get();

        $items = $collection->items;

        $rows = [];

        foreach ($categories as $cat) {

            $catItems = $items->where('category_id',$cat->id);

            if ($catItems->count()) {

                $qty = $catItems->sum('qty');

                $totalWeight = $catItems->sum(fn($it) => $it->weight_kg ?? 0);

                $perItem = $qty ? ($totalWeight / $qty) : 0;

                $displayName = $catItems->first()->category_name ?: $cat->name;

            } else {

                $qty = 0;
                $totalWeight = 0;
                $perItem = 0;
                $displayName = $cat->name;
            }

            $rows[] = (object)[
                'name' => $displayName,
                'qty' => $qty,
                'per_item_weight' => $perItem,
                'total_weight' => $totalWeight,
                'ewc_code' => $cat->ewc_code,
                'component' => $cat->component,
                'concentration' => $cat->concentration,
                'physical_form' => $cat->physical_form,
                'hazard_codes' => $cat->hazard_codes,
            ];
        }

        $totalWeight = collect($rows)->sum('total_weight');

        $consignmentCode =
            $collection->collection_code
            ?: $collection->collection_number
            ?: $collection->id;

        $html = view('pdf.hazardous', compact(
            'collection',
            'rows',
            'totalWeight',
            'consignmentCode'
        ))->render();

        $mpdf = $this->makeMpdf();
        $mpdf->WriteHTML($html);

        return response($mpdf->Output("hazardous_{$collection->id}.pdf",'S'),200,[
            'Content-Type'=>'application/pdf',
            'Content-Disposition'=>'inline; filename="hazardous_'.$collection->id.'.pdf"',
        ]);
    }

    private function groupItemsByCategory($items)
    {
        return $items->groupBy('category_id')->map(function ($group) {
            $cat = $group->first()->category;

            $qty = (int) $group->sum('qty');

            // total weight is sum of row weights
            $totalWeight = (float) $group->sum(fn($it) => (float) ($it->weight_kg ?? 0));

            $perItem = $qty > 0 ? $totalWeight / $qty : 0;

            return (object)[
                'category' => $cat,
                'qty' => $qty,
                'per_item_weight' => round($perItem, 3),
                'total_weight' => round($totalWeight, 3),
            ];
        })->values();
    }

    private function makeMpdf(): Mpdf
    {
        return new Mpdf([
            'format' => 'A4',
            'margin_left' => 8,
            'margin_right' => 8,
            'margin_top' => 8,
            'margin_bottom' => 8,
            'default_font' => 'dejavusans',
        ]);
    }
}