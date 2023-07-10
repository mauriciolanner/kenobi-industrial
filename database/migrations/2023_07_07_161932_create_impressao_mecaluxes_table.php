<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImpressaoMecaluxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('impressao_mecaluxes', function (Blueprint $table) {
            $table->id();
            $table->string('CODIGO_APONTAMENTO')->nullable();
            $table->string('APONTAMENTO_MES')->nullable();
            $table->string('ID_INTEGRACAO_MES')->nullable();
            $table->string('DtMov')->nullable();
            $table->string('QUANTIDADE')->nullable();
            $table->string('PRODUTO')->nullable();
            $table->string('RECEITA')->nullable();
            $table->string('OP')->nullable();
            $table->string('ARMAZEM')->nullable();
            $table->string('ErrDescription')->nullable();
            $table->string('IDPCFACTORY')->nullable();
            $table->string('RECURSO')->nullable();
            $table->string('IMPRESSO')->nullable();
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
        Schema::dropIfExists('impressao_mecaluxes');
    }
}
