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
        Schema::create('portal_modulos_secoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('modulo_id');
            $table->string('secao');
            $table->string('url');
            $table->string('icone');
            $table->boolean('ativo');
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('modulo_id')->references('id')->on('portal_modulos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('portal_modulos_secoes');
    }
};
