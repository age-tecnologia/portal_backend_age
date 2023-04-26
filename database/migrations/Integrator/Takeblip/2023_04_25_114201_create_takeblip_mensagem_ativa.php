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
        Schema::create('takeblip_mensagem_ativa', function (Blueprint $table) {
            $table->id();
            $table->string('cliente');
            $table->string('numero_original');
            $table->string('numero_enviado');
            $table->string('lote');
            $table->date('vencimento');
            $table->dateTime('data_envio_whatsapp');
            $table->boolean('sucesso');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('takeblip_mensagem_ativa');
    }
};
