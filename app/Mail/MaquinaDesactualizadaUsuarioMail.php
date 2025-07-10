<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class MaquinaDesactualizadaUsuarioMail extends Mailable
{
    use Queueable, SerializesModels;

    public $maquinas;
    public $fechaInicio;
    public $fechaFin;

    /**
     * @param \Illuminate\Support\Collection $maquinas
     * @param \Carbon\Carbon $fechaInicio
     * @param \Carbon\Carbon $fechaFin
     */
    public function __construct(Collection $maquinas, Carbon $fechaInicio, Carbon $fechaFin)
    {
        $this->maquinas = $maquinas;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function build()
    {
        return $this->subject('⚠️ Reporte no enviado: máquinas desactualizadas')
                    ->markdown('emails.maquinas_desactualizadas');
    }
}
