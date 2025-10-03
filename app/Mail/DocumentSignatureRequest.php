<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Document;

class DocumentSignatureRequest extends Mailable
{
    public $document;

    public function __construct(Document $document)
    {
        $this->document = $document;
    }

    public function build()
    {
        $url = route('documents.sign', $this->document->signature_token);

        return $this->subject('Please Sign Your Document')
            ->view('emails.document-signature')
            ->with([
                'document' => $this->document,
                'url' => $url,
            ]);
    }
}
