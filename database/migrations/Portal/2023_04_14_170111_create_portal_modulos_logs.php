<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portal_modulos_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('modulo_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('erro');
            $table->json('log');
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('modulo_id')->references('id')->on('portal_modulos');
            $table->foreign('user_id')->references('id')->on('portal_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('portal_modulos_logs');
    }
};
