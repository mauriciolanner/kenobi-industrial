<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEtiquetaFardosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('etiqueta_fardos', function (Blueprint $table) {
            $table->id();
            $table->string('OP')->nullable();
            $table->string('VIA')->nullable();
            $table->string('PRODUTO')->nullable();
            $table->string('QTD_ETIQUETAS')->nullable();
            $table->string('user_id')->nullable();
            $table->string('user_name')->nullable();
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
        Schema::dropIfExists('etiqueta_fardos');
    }
}
