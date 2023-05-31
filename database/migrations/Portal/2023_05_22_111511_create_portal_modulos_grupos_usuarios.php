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
        Schema::create('portal_modulos_grupos_usuarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('grupo_id');
            $table->unsignedBigInteger('adicionado_por');
            $table->unsignedBigInteger('atualizado_por');
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('user_id')->references('id')->on('portal_users');
            $table->foreign('grupo_id')->references('id')->on('portal_modulos_grupos');
            $table->foreign('adicionado_por')->references('id')->on('portal_users');
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
        Schema::dropIfExists('portal_modulos_grupos_usuarios');
    }
};
