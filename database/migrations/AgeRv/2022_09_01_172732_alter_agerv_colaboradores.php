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
            $table->unsignedBigInteger('funcao_id')->after('user_id')->default(2);
            $table->foreign('funcao_id')->references('id')->on('portal_colaboradores_funcoes');
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
            $table->dropForeign(['funcao_id']);
            $table->dropColumn('funcao_id');
        });
    }
};
