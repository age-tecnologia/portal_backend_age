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
        Schema::create('agecontrol_usuarios_permitidos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('funcao_id');
            $table->unsignedBigInteger('setor_id');
            $table->unsignedBigInteger('nivel_de_acesso_id');
            $table->unsignedBigInteger('criado_por');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('portal_users');
            $table->foreign('funcao_id')->references('id')->on('portal_colaboradores_funcoes');
            $table->foreign('setor_id')->references('id')->on('portal_colaboradores_setores');
            $table->foreign('criado_por')->references('id')->on('portal_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agecontrol_usuarios_permitidos');
    }
};
