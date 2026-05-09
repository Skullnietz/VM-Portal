<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendingDesactivadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $maquina;

    public function __construct($maquina)
    {
        $this->maquina = $maquina;
    }

    public function build()
    {
        return $this->subject('🚨 Máquina Vending Desactivada')
                    ->view('emails.vending_alerta');
    }
}