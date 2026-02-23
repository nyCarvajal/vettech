<?php

namespace App\Mail;

use App\Models\Clinica;
use App\Models\Owner;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClienteVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Clinica $clinica,
        public Owner $cliente,
        public string $verificationUrl
    ) {
    }

    public function build(): self
    {
        return $this->subject('Confirma tu correo en ' . $this->clinica->nombre)
            ->markdown('emails.clientes.verify')
            ->with([
                'clinica' => $this->clinica,
                'cliente' => $this->cliente,
                'verificationUrl' => $this->verificationUrl,
            ]);
    }
}
