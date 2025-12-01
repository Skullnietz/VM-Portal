<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistorialRellenoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Historial_Relleno', function (Blueprint $table) {
            $table->id('Id_Historial');
            $table->integer('Id_Configuracion')->nullable();
            $table->integer('Id_Maquina')->nullable();
            $table->integer('Id_Articulo')->nullable();
            $table->integer('Cantidad_Anterior')->nullable();
            $table->integer('Cantidad_Rellenada')->nullable();
            $table->integer('Cantidad_Nueva')->nullable();
            $table->dateTime('Fecha_Relleno')->useCurrent();
            $table->integer('Id_Usuario')->nullable();
            $table->string('Tipo_Usuario')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('historial_relleno');
    }
}
