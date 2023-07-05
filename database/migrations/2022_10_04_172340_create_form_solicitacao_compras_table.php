<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormSolicitacaoComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_solicitacao_compras', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_number');
            $table->foreign('form_number')->references('id')->on('form_numbers');
            ///campos do formulário
            $table->string('solicitante')->nullable();
            $table->string('aprovador')->nullable();
            $table->string('filial')->nullable();
            $table->string('filialCod')->nullable();
            $table->string('nSolicitacao')->nullable();
            $table->string('emergencia')->nullable();
            $table->string('emergenciaMotivo')->nullable();
            $table->string('centroCusto')->nullable();
            $table->date('codCentroCusto')->nullable();
            //campos padrão
            $table->unsignedBigInteger('create_user_id');
            $table->foreign('create_user_id')->references('id')->on('users');
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
        Schema::dropIfExists('form_solicitacao_compras');
    }
}
