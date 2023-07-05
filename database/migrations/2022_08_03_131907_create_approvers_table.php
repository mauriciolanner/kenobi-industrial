<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApproversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('approvers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('form_number');
            $table->string('name');
            $table->integer('ordem');
            $table->integer('type')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->longText('group')->nullable();
            $table->bigInteger('create_user_id')->nullable();
            $table->unsignedBigInteger('form_id');
            $table->foreign('form_id')->references('id')->on('forms');
            $table->unsignedBigInteger('task_forms_id');
            $table->foreign('task_forms_id')->references('id')->on('task_forms');
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
        Schema::dropIfExists('approvers');
    }
}
