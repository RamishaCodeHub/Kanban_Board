<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WebsiteLinkEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $email;
    protected $token;

    public function __construct($email, $token)
    {
        $this->email = $email;
        $this->token = $token;
    }

    public function build()
    {
        return $this->view('emails.website-link-email')
                    ->from('alizykhan2011@gmail.com') 
                    ->with([
                        'email' => $this->email,
                        'token' => $this->token
                    ]);
    }
}
