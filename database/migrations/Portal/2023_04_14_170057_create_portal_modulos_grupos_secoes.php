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
        Schema::create('portal_modulos_grupos_secoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('grupo_id');
            $table->unsignedBigInteger('secao_id');
            $table->unsignedBigInteger('criado_por');
            $table->unsignedBigInteger('atualizado_por');
            $table->boolean('ativo');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('grupo_id')->references('id')->on('portal_modulos_grupos');
            $table->foreign('secao_id')->references('id')->on('portal_modulos_secoes');
            $table->foreign('criado_por')->references('id')->on('portal_users');
            $table->foreign('atualizado_por')->references('id')->on('portal_users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('portal_modulos_grupos_secoes');
    }
};
