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
        Schema::create('portal_modulos_grupos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('modulo_id');
            $table->string('grupo')->unique();
            $table->unsignedBigInteger('criado_por');
            $table->unsignedBigInteger('atualizado_por');
            $table->boolean('ativo');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('modulo_id')->references('id')->on('portal_modulos');
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
        Schema::dropIfExists('portal_modulos_grupos');
    }
};
