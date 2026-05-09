<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtensibilityFieldsToConfiguracionReportes extends Migration
{
    public function up()
    {
        Schema::table('Configuracion_Reportes', function (Blueprint $table) {
            $table->string('Tipo_Reporte', 50)->default('consumo_general')->after('Recibir_Notificaciones');
            $table->dateTime('Ultimo_Envio')->nullable()->after('Tipo_Reporte');
            $table->boolean('Activo')->default(true)->after('Ultimo_Envio');
            $table->string('Plantilla', 100)->nullable()->after('Activo');
        });
    }

    public function down()
    {
        Schema::table('Configuracion_Reportes', function (Blueprint $table) {
            $table->dropColumn(['Tipo_Reporte', 'Ultimo_Envio', 'Activo', 'Plantilla']);
        });
    }
}
