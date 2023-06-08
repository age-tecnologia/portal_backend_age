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
        Schema::create('agetools_agenda_executadas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('executada_por');
            $table->string('protocolo');
            $table->dateTime('data_inicio_atendimento');
            $table->dateTime('data_fim_atendimento');
            $table->dateTime('data_inicio_agendamento');
            $table->dateTime('data_fim_agendamento');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('executada_por')->references('id')->on('portal_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agetools_agenda_executadas');
    }
};
