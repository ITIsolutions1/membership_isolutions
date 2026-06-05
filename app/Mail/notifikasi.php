<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class notifikasi extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $event;
    public $nama;
    public $pesan;
    public function __construct($event, $nama, $pesan)
        {
            $this->event = $event;
            $this->nama = $nama;
                $this->pesan = $pesan;
        }

    public function build()
        {
            return $this->subject('Notifikasi Acara Baru')
                        ->view('emails.email_template.notifikasi')
                        ->with(['event' => $this->event,
                                'nama' => $this->nama,
                                 'pesan' => $this->pesan
                            ]);
        }

    /**
     * Get the message envelope.
     */
    // public function envelope(): Envelope
    // {
    //     return new Envelope(
    //         subject: 'Notifikasi',
    //     );
    // }

    /**
     * Get the message content definition.
     */
    // public function content(): Content
    // {
    //     return new Content(
    //         view: 'view.name',
    //     );
    // }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    // public function attachments(): array
    // {
    //     return [];
    // }
}
