<?php

namespace App\Mail;

use App\Models\Clinica;
use App\Models\Owner;
use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NuevaReservaClinicaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Clinica $clinica,
        public Owner $cliente,
        public Reserva $reserva
    ) {
    }

    public function build(): self
    {
        return $this
            ->subject('Nueva Reserva [AluresTech]')
            ->markdown('emails.clinicas.nueva-reserva');
    }
}
