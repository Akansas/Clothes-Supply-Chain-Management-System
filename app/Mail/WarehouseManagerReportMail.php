<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WarehouseManagerReportMail extends Mailable
{
    use Queueable, SerializesModels;
    public $inventory;

    /**
     * Create a new message instance.
     */
    public function __construct($inventory)
    {
        $this->inventory= $inventory;
    }

    public function build()
    {
        return $this->subject('Warehouse Inventory Report')
        ->view('emails.inventory_report')
                            ->with(['inventory'=> $this->inventory]);
    }

}
