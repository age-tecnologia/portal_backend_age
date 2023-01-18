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
        Schema::table('agerv_colaboradores_meta', function (Blueprint $table) {
            $table->string('ano_competencia')->after('mes_competencia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agerv_colaboradores_meta', function (Blueprint $table) {
            $table->dropColumn('ano_competencia');
        });
    }
};
