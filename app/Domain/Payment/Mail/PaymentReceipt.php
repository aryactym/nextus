<?php

declare(strict_types=1);

namespace App\Domain\Payment\Mail;

use App\Domain\Payment\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Bukti pembayaran '.config('app.name'));
    }

    public function content(): Content
    {
        return new Content(view: 'emails.receipt');
    }
}
