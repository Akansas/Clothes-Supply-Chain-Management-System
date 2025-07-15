<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RetailerReportMail extends Mailable
{
    use Queueable, SerializesModels;
    public $orders;
    public $retailer;

    /**
     * Create a new message instance.
     */
    public function __construct($orders, $retailer)
    {
        $this->orders =$orders;
        $this->retailer=$retailer;
    }

    public function build()
    {
       return $this->subject('📦 Retailer Daily Report')
                    ->view('emails.retailer_report')
                    ->with([
                        'orders' => $this->orders,
                        'retailer' => $this->retailer,
               ]);

    }
}
