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
        Schema::create('agetools_mailers', function (Blueprint $table) {
            $table->id();
            $table->string('mailer')->unique();
            $table->json('configuracao');
            $table->integer('limite_diario');
            $table->unsignedBigInteger('criado_por');
            $table->unsignedBigInteger('modificado_por');
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('criado_por')->references('id')->on('portal_users');
            $table->foreign('modificado_por')->references('id')->on('portal_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agetools_mailers');
    }
};
