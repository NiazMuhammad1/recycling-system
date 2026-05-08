<?php

namespace App\Jobs;

use App\Mail\CollectionPdfsMail;
use App\Models\CollectionPdfEmail;
use App\Http\Controllers\CollectionPdfController;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendCollectionPdfJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $pdfEmail;

    /**
     * Create a new job instance.
     */
    public function __construct(CollectionPdfEmail $pdfEmail)
    {
        $this->pdfEmail = $pdfEmail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $collection = $this->pdfEmail->collection;

        $pdfController = new CollectionPdfController();

        $attachments = [];

        $pdfMap = [

            'weee' => [
                'method' => 'generateWeeeDisposalPdf',
                'name' => 'weee-disposal.pdf',
            ],

            'duty' => [
                'method' => 'generateDutyOfCarePdf',
                'name' => 'duty-of-care.pdf',
            ],

            'hazard' => [
                'method' => 'generateHazardousPdf',
                'name' => 'hazardous.pdf',
            ],

            'audit' => [
                'method' => 'generateAuditReportPdf',
                'name' => 'audit-report.pdf',
            ],

            'data_destruction' => [
                'method' => 'generateDataDestructionPdf',
                'name' => 'data-destruction.pdf',
            ],
        ];

        foreach ($this->pdfEmail->pdfs as $pdfType) {

            if (!isset($pdfMap[$pdfType])) {
                continue;
            }

            $method = $pdfMap[$pdfType]['method'];

            $fileName = $pdfMap[$pdfType]['name'];

            $pdfPath = $pdfController->$method($collection);

            if ($pdfPath && file_exists($pdfPath)) {

                $attachments[] = [
                    'path' => $pdfPath,
                    'name' => $fileName,
                ];
            }
        }

        Mail::to($this->pdfEmail->email)
            ->send(
                new CollectionPdfsMail(
                    $collection,
                    $attachments
                )
            );

        $this->pdfEmail->update([
            'status' => 'sent',
            'sent_at' => now(),
            'error' => null,
        ]);

        // OPTIONAL: delete temp files
        foreach ($attachments as $attachment) {

            if (file_exists($attachment['path'])) {
                @unlink($attachment['path']);
            }
        }
    }

    /**
     * Handle failed job.
     */
    public function failed(\Throwable $e): void
    {
        $this->pdfEmail->update([
            'status' => 'failed',
            'error' => $e->getMessage(),
        ]);
    }
}