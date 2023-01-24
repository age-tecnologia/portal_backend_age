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
        Schema::create('agecontrol_veiculos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('condutor_id');
            $table->unsignedBigInteger('tipo_veiculo_id');
            $table->string('fabricante', 50);
            $table->string('modelo', 50);
            $table->float('capacidade_tanque');
            $table->float('media_km_litro');
            $table->integer('quilometragem_inicial');
            $table->float('distancia_sede_casa');
            $table->unsignedBigInteger('modalidade_id');
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('condutor_id')->references('id')->on('agecontrol_condutores');
            $table->foreign('tipo_veiculo_id')->references('id')->on('agecontrol_veiculo_tipo');
            $table->foreign('modalidade_id')->references('id')->on('agecontrol_veiculo_modalidade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agecontrol_veiculos');
    }
};
