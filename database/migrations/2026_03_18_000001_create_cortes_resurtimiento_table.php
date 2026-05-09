<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCortesResurtimientoTable extends Migration
{
    public function up()
    {
        Schema::create('Cortes_Resurtimiento', function (Blueprint $table) {
            $table->id('Id_Corte');
            $table->integer('Id_Maquina');
            $table->string('Tipo_Corte', 10); // 'PRE' or 'POST'
            $table->dateTime('Fecha_Corte')->useCurrent();
            $table->integer('Id_Usuario')->nullable();
            $table->string('Tipo_Usuario', 20)->nullable(); // 'Admin' or 'Operador'
            $table->unsignedBigInteger('Id_Corte_Pre')->nullable(); // POST references its PRE
            $table->string('Notas', 500)->nullable();
            $table->timestamps();

            $table->foreign('Id_Corte_Pre')
                ->references('Id_Corte')
                ->on('Cortes_Resurtimiento')
                ->onDelete('no action')
                ->onUpdate('no action');
        });
    }

    public function down()
    {
        Schema::dropIfExists('Cortes_Resurtimiento');
    }
}
