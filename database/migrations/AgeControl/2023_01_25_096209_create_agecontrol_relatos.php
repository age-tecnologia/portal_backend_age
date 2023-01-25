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
        Schema::create('agecontrol_relatos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('condutor_id');
            $table->float('quilometragem_relatada');
            $table->float('quilometragem_aprovada')->nullable();
            $table->unsignedBigInteger('periodo_id');
            $table->unsignedBigInteger('aprovador_id')->nullable();
            $table->string('nome_foto');
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('condutor_id')->references('id')->on('agecontrol_condutores');
            $table->foreign('periodo_id')->references('id')->on('agecontrol_relato_periodos');
            $table->foreign('aprovador_id')->references('id')->on('portal_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agecontrol_relatos');
    }
};
