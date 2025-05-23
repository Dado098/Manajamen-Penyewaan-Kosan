<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $url;  // pastikan public supaya bisa diakses di blade

    /**
     * Create a new message instance.
     *
     * @param string $url  URL reset password yang dikirim ke view
     */
    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Reset Password - SenangKos')
                    ->view('emails.custom-reset-password')
                    ->with([
                        'url' => $this->url,
                    ]);
    }
}
