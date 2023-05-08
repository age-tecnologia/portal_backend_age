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
        Schema::create('agetools_mailers_lotes_enviados', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mailer_id');
            $table->unsignedBigInteger('template_id');
            $table->unsignedBigInteger('enviado_por');
            $table->timestamps();

            $table->foreign('mailer_id')->references('id')->on('agetools_mailers');
            $table->foreign('template_id')->references('id')->on('agetools_mailers_templates');
            $table->foreign('enviado_por')->references('id')->on('portal_users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agetools_mailers_lotes_enviados');
    }
};
