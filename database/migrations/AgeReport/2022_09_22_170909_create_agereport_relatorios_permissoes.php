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
        Schema::create('agereport_relatorios_permissoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('relatorio_id');
            $table->unsignedBigInteger('permitido_por');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('portal_users');
            $table->foreign('relatorio_id')->references('id')->on('agereport_relatorios');
            $table->foreign('permitido_por')->references('id')->on('portal_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agereport_relatorios_permissoes');
    }
};
