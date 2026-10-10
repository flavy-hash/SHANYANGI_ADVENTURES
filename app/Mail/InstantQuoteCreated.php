<?php

namespace App\Mail;

use App\Models\Quote;
use App\Support\QuotePdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to the office (SITE_EMAIL) when a visitor generates an instant quote on a package page.
 */
class InstantQuoteCreated extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Quote $quote)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->quote->customer_email, $this->quote->customer_name)],
            subject: 'Instant quote '.$this->quote->reference.' for '.$this->quote->customer_name,
        );
    }

    public function content(): Content
    {
        return new Content(text: 'emails.instant-quote');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => QuotePdf::output($this->quote), $this->quote->pdfFilename())->withMime('application/pdf'),
        ];
    }
}
