<?php

namespace App\Http\Controllers;

use App\Jobs\SendCollectionPdfJob;
use App\Models\Collection;
use Illuminate\Http\Request;
use App\Models\CollectionPdfEmail;

class CollectionPdfEmailController extends Controller
{
    /**
     * View page
     */
    public function index(Collection $collection)
    {
        $pdfEmails = $collection->pdfEmails()
            ->latest()
            ->paginate(20);

        return view(
            'collections.pdf-emails.index',
            compact('collection', 'pdfEmails')
        );
    }

    /**
     * Send PDFs
     */
    public function send(Request $request, Collection $collection)
    {
        $request->validate([

            'emails' => 'required|array',

            'emails.*' => 'required|email',

            'pdfs' => 'required|array',

            'pdfs.*' => 'required|string',
        ]);

        foreach ($request->emails as $email) {

            $record = CollectionPdfEmail::create([

                'collection_id' => $collection->id,

                'email' => $email,

                'pdfs' => $request->pdfs,

                'status' => 'pending',

                'sent_by' => auth()->id(),
            ]);

            SendCollectionPdfJob::dispatch($record)
                ->onQueue('emails');
        }

        return redirect()
            ->back()
            ->with(
                'success',
                'PDF emails queued successfully.'
            );
    }

    /**
     * Show single record
     */
    public function show(CollectionPdfEmail $pdfEmail)
    {
        return view(
            'collections.pdf-emails.show',
            compact('pdfEmail')
        );
    }

    /**
     * Resend
     */
    public function resend(CollectionPdfEmail $pdfEmail)
    {
        $newRecord = CollectionPdfEmail::create([

            'parent_id' => $pdfEmail->id,

            'collection_id' => $pdfEmail->collection_id,

            'email' => $pdfEmail->email,

            'pdfs' => $pdfEmail->pdfs,

            'status' => 'pending',

            'sent_by' => auth()->id(),
        ]);

        SendCollectionPdfJob::dispatch($newRecord)
            ->onQueue('emails');

        return redirect()
            ->back()
            ->with(
                'success',
                'PDF email queued again.'
            );
    }
}