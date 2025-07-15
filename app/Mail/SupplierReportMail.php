namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupplierReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $orders;

    public function __construct($orders)
    {
        $this->orders = $orders;
    }

    public function build()
    {
        return $this->subject('Your Purchase Orders for Today')
                    ->view('emails.supplier_report');
 }
}