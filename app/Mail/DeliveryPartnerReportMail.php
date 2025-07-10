<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DeliveryPartnerReportMail extends Mailable
{
    use Queueable, SerializesModels;
    public $shipments;

    /**
     * Create a new message instance.
     */
    public function __construct($shipments)
    {
        $this->shipments = $shipments;
    }
    
    public function build()
    {
        return $this->subject('Your Daily Delivery Report')
        ->view('emails.delivery_report')
                             ->with(['shipments'=> $this->shipments]);
    }

    
}
