<?php

namespace App\Mail;

use App\Models\TripRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Sent to the office (SITE_EMAIL) when a visitor submits the trip request form.
 */
class TripRequestReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public TripRequest $tripRequest)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->tripRequest->email, $this->tripRequest->name)],
            subject: 'New trip request from '.$this->tripRequest->name,
        );
    }

    public function content(): Content
    {
        return new Content(text: 'emails.trip-request');
    }
}
