<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorteDetalleTable extends Migration
{
    public function up()
    {
        Schema::create('Corte_Detalle', function (Blueprint $table) {
            $table->id('Id_Detalle');
            $table->unsignedBigInteger('Id_Corte');
            $table->integer('Id_Configuracion');
            $table->integer('Id_Articulo')->nullable();
            $table->string('Seleccion', 20)->nullable();
            $table->string('Talla', 50)->nullable();
            $table->integer('Stock_Actual')->default(0);
            $table->integer('Cantidad_Max')->default(0);
            $table->integer('Cantidad_Necesaria')->default(0); // Cantidad_Max - Stock_Actual (PRE)
            $table->integer('Stock_Post')->nullable(); // Stock después del resurtido (POST)
            $table->integer('Cantidad_Rellenada')->nullable(); // Stock_Post - Stock_Actual (POST)
            $table->timestamps();

            $table->foreign('Id_Corte')
                ->references('Id_Corte')
                ->on('Cortes_Resurtimiento')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('Corte_Detalle');
    }
}
