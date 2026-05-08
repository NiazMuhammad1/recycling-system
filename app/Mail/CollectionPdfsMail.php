<?php

namespace App\Mail;

use App\Models\Collection;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CollectionPdfsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $collection;

    public $attachmentsData;

    /**
     * Create a new message instance.
     */
    public function __construct(
        Collection $collection,
        array $attachmentsData
    ) {
        $this->collection = $collection;
        $this->attachmentsData = $attachmentsData;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $mail = $this->subject('Collection PDFs')
            ->view('emails.collection_pdfs');

        foreach ($this->attachmentsData as $attachment) {

            if (file_exists($attachment['path'])) {

                $mail->attach(
                    $attachment['path'],
                    [
                        'as' => $attachment['name'],
                        'mime' => 'application/pdf',
                    ]
                );
            }
        }

        return $mail;
    }
}