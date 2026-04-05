<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
class CollectionPdfsMail extends Mailable
{
    public $collection;

    protected $dutyPdf;
    protected $hazardPdf;

    public function __construct($collection, $dutyPdf, $hazardPdf)
    {
        $this->collection = $collection;
        $this->dutyPdf = $dutyPdf;
        $this->hazardPdf = $hazardPdf;
    }

    public function build()
    {
        return $this->subject('Collection Documents')
            ->view('emails.collection_pdfs')
            ->attachData($this->dutyPdf, 'duty_of_care_'.$this->collection->id.'.pdf')
            ->attachData($this->hazardPdf, 'hazardous_'.$this->collection->id.'.pdf');
    }
}