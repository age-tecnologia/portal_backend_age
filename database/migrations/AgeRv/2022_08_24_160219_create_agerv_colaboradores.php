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
        Schema::create('agerv_colaboradores', function (Blueprint $table) {
            $table->id();
            $table->string('nome', 255);
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('funcao_id')->default(1);
            $table->unsignedBigInteger('canal_id')->default(1);
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('user_id')->references('id')->on('portal_users');
            $table->foreign('funcao_id')->references('id')->on('portal_colaboradores_funcoes');
            $table->foreign('canal_id')->references('id')->on('agerv_colaboradores_canais');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('colaboradores');
    }
};
