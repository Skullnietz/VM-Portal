<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReporteEmpleadosMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mensaje;
    public $filename;

    public function __construct($mensaje, $filename)
    {
        $this->mensaje = $mensaje;
        $this->filename = $filename;
    }

    public function build()
    {
        return $this->subject('Reporte de Consumos')
                    ->view('emails.reporte_consumos')
                    ->attach(storage_path("app/{$this->filename}"));
    }
}
