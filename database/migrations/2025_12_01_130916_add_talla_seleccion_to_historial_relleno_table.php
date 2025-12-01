<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTallaSeleccionToHistorialRellenoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('Historial_Relleno', function (Blueprint $table) {
            $table->string('Seleccion')->nullable()->after('Id_Articulo');
            $table->string('Talla')->nullable()->after('Seleccion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('Historial_Relleno', function (Blueprint $table) {
            $table->dropColumn(['Seleccion', 'Talla']);
        });
    }
}
