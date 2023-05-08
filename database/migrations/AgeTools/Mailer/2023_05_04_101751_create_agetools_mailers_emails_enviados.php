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
        Schema::create('agetools_mailers_emails_enviados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lote_id');
            $table->string('email_destinatario');
            $table->boolean('status');
            $table->json('erro')->nullable();
            $table->timestamps();

            $table->foreign('lote_id')->references('id')->on('agetools_mailers_lotes_enviados');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agetools_mailers_emails_enviados');
    }
};
