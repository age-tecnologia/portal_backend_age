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
        Schema::table('agerv_colaboradores', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_comissao_id')->after('canal_id')->default(2);
            $table->foreign('tipo_comissao_id')->references('id')->on('agerv_colaboradores_canais');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agerv_colaboradores', function (Blueprint $table) {
            $table->dropForeign(['tipo_comissao_id']);
            $table->dropColumn('tipo_comissao_id');
        });
    }
};
