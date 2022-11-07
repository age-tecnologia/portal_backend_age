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
        Schema::table('agereport_relatorios', function (Blueprint $table) {
            $table->string('cabecalhos', 500)->after('query');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agereport_relatorios', function (Blueprint $table) {
            $table->dropColumn('cabecalhos');
        });
    }
};
