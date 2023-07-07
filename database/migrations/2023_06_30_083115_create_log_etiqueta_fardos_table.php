<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogEtiquetaFardosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_etiqueta_fardos', function (Blueprint $table) {
            $table->id();
            $table->string('usuario');
            $table->string('log');
            $table->string('op');
            $table->integer('num_impressoes');
            $table->string('motivo_da_impressao')->nullable();
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
        Schema::dropIfExists('log_etiqueta_fardos');
    }
}
