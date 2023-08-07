<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddESTORNOTableImpressaoMecaluxes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('impressao_mecaluxes', function (Blueprint $table) {
            //
            $table->string('ESTORNO')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('impressao_mecaluxes', function (Blueprint $table) {
            //
            $table->dropColumn('ESTORNO');
        });
    }
}
