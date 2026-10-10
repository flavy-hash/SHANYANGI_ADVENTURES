<?php

namespace App\Mail;

use App\Models\Quote;
use App\Support\QuotePdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sends a quotation to the customer with the PDF attached.
 */
class QuoteMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Quote $quote)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your quotation '.$this->quote->reference.' from Shanyangi Adventures',
            replyTo: array_filter([config('site.email')]),
        );
    }

    public function content(): Content
    {
        return new Content(text: 'emails.quote');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => QuotePdf::output($this->quote), $this->quote->pdfFilename())->withMime('application/pdf'),
        ];
    }
}
