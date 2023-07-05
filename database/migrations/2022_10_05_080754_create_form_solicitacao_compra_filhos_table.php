<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormSolicitacaoCompraFilhosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('form_solicitacao_compra_filhos', function (Blueprint $table) {
            $table->id();

            $table->string('codProduto')->nullable();
            $table->string('descProduto')->nullable();
            $table->string('UM')->nullable();
            $table->dateTime('dataNecessidade')->nullable();
            $table->string('qtd')->nullable();
            $table->string('justificativa')->nullable();

            $table->string('form_pai_id');
            $table->unsignedBigInteger('create_user_id');
            $table->foreign('create_user_id')->references('id')->on('users');
            $table->bigInteger('form_number');
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
        Schema::dropIfExists('form_solicitacao_compra_filhos');
    }
}
