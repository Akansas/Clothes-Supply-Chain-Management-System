<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ManufacturerReportMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct($orders)
    {
        $this->orders =$orders;
    }

    public function build()
    {
        return $this->subject('Daily Production Report')
        ->view('emails.manufacturer_report')
                  ->with(['orders'=>$this->orders,]);
    }

}
