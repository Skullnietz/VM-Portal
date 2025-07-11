<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class ReporteEmpleadosMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mensaje;
    public $filename;
    public $fechaInicio;
    public $fechaFin;

    public function __construct($mensaje, $filename, Carbon $fechaInicio, Carbon $fechaFin)
    {
        $this->mensaje = $mensaje;
        $this->filename = $filename;
        $this->fechaInicio = $fechaInicio;
        $this->fechaFin = $fechaFin;
    }

    public function build()
    {
        $rango = $this->fechaInicio->format('d/m/Y') . ' al ' . $this->fechaFin->format('d/m/Y');

        return $this->subject("📊 Reporte de Consumos: {$rango}")
                    ->markdown('emails.reporte_consumos')
                    ->attach(storage_path("app/{$this->filename}"));
    }
}
