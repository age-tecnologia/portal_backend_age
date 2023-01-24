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
        Schema::create('agecontrol_condutores', function (Blueprint $table) {
            $table->id();
            $table->string('primeiro_nome', 15);
            $table->string('segundo_nome', 155);
            $table->string('endereco', 300);
            $table->unsignedBigInteger('cidade_id');
            $table->unsignedBigInteger('grupo_id');
            $table->unsignedBigInteger('servico_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('user_id')->references('id')->on('portal_users');
            $table->foreign('cidade_id')->references('id')->on('portal_cidades');
            $table->foreign('grupo_id')->references('id')->on('portal_colaboradores_grupos');
            $table->foreign('servico_id')->references('id')->on('agecontrol_servicos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agecontrol_condutores');
    }
};
