<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormModelosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_modelos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_number');
            $table->foreign('form_number')->references('id')->on('form_numbers');
            //campos
            $table->string('usuarioAbertura')->nullable();
            $table->string('dataAbertura')->nullable();
            $table->string('solicitante')->nullable();
            $table->string('chapaSolicitante')->nullable();
            $table->string('exemploData')->nullable();
            $table->string('exemploTexto')->nullable();
            //campos padrão
            $table->bigInteger('create_user_id');
            $table->unsignedBigInteger('form_id');
            $table->foreign('form_id')->references('id')->on('forms');
            $table->string('status');
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
        Schema::dropIfExists('form_modelos');
    }
}
