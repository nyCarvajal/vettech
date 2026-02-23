<?php

namespace App\Mail;

use App\Models\Owner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OwnerCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Owner $owner,
        public string $plainPassword
    ) {
    }

    public function build(): self
    {
        return $this->subject('Acceso a tu portal de tutor')
            ->markdown('emails.owners.credentials')
            ->with([
                'owner' => $this->owner,
                'plainPassword' => $this->plainPassword,
            ]);
    }
}
