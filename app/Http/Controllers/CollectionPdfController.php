<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Mpdf\Mpdf;

class CollectionPdfController extends Controller
{
    public function dutyOfCare(Collection $collection)
    {
        $collection->load(['items.category', 'user', 'client']); // adjust relations if needed

        // Only categories relevant to Duty of Care
        $items = $collection->items
            ->filter(fn($it) => $it->category && in_array($it->category->type, ['duty_of_care', 'both'], true));

        $rows = $this->groupItemsByCategory($items);

        $totalItems = $rows->sum('qty');
        $totalWeight = $rows->sum('total_weight');    

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
        $collection->load(['items.category', 'user', 'client']); // adjust relations if needed

        // Only categories relevant to Hazardous
        $items = $collection->items
            ->filter(fn($it) => $it->category && in_array($it->category->type, ['hazard', 'both'], true));

        $rows = $this->groupItemsByCategory($items);

        $totalWeight = $rows->sum('total_weight');

        $html = view('pdf.hazardous', compact('collection', 'rows', 'totalWeight'))->render();

        $mpdf = $this->makeMpdf();
        $mpdf->WriteHTML($html);

        return response($mpdf->Output("hazardous_{$collection->id}.pdf", 'S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="hazardous_'.$collection->id.'.pdf"',
        ]);
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

    private function groupItemsByCategory($items)
    {
        // groups by category_id and sums qty and weight
        return $items->groupBy('category_id')->map(function ($group) {
            $cat = $group->first()->category;

            $qty = $group->sum('qty');
            $totalWeight = $group->sum(function ($it) {
                return (float)($it->weight_kg ?? 0);
            });

            $perItem = $qty > 0 ? ($totalWeight / $qty) : 0;

            return (object)[
                'category' => $cat,
                'qty' => $qty,
                'total_weight' => round($totalWeight, 2),
                'per_item_weight' => round($perItem, 2),
            ];
        })->values();
    }
}