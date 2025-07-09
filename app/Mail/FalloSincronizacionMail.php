<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FalloSincronizacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $maquinas;
    public $nombrePlanta;

    public function __construct($maquinas, $nombrePlanta)
    {
        $this->maquinas = $maquinas;
        $this->nombrePlanta = $nombrePlanta;
    }

    public function build()
    {
        return $this->subject("⚠️ Falla de sincronización en planta: {$this->nombrePlanta}")
                    ->view('emails.fallo_sincronizacion');
    }
}
