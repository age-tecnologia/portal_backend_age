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
        Schema::create('agerv_colaborador', function (Blueprint $table) {
            $table->id();
            $table->integer('voalle_id')->nullable()->unique();
            $table->string('nome');
            $table->date('data_admissao')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->unique();
            $table->unsignedBigInteger('funcao_id')->nullable();
            $table->unsignedBigInteger('canal_id')->nullable();
            $table->unsignedBigInteger('tipo_comissao_id')->nullable();
            $table->unsignedBigInteger('coordenador_id')->nullable();
            $table->unsignedBigInteger('gerente_id')->nullable();
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('user_id')->references('id')->on('portal_users');
            $table->foreign('funcao_id')->references('id')->on('portal_colaboradores_funcoes');
            $table->foreign('canal_id')->references('id')->on('agerv_colaboradores_canais');
            $table->foreign('tipo_comissao_id')->references('id')->on('agerv_colaboradores_canais');
            $table->foreign('coordenador_id')->references('id')->on('portal_users');
            $table->foreign('gerente_id')->references('id')->on('portal_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agerv_colaborador');
    }
};
