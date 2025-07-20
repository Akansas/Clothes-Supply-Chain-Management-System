<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupplierReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $supplier;
    public $inventory;
    public $pendingOrders;

    /**
     * Create a new message instance.
     */
    public function __construct($supplier, $inventory, $pendingOrders)
    {
        $this->supplier = $supplier;
        $this->inventory = $inventory;
        $this->pendingOrders = $pendingOrders;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Daily Supplier Report')
            ->view('emails.supplier_report');
    }
}