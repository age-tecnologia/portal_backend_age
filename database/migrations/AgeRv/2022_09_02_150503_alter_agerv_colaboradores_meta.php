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
        Schema::table('agerv_colaboradores_meta', function(Blueprint $table) {
            $table->unsignedBigInteger('colaborador_id')->after('id');
            $table->foreign('colaborador_id')->references('id')->on('agerv_colaboradores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agerv_colaboradores_meta', function(Blueprint $table) {
            $table->dropForeign(['colaborador_id']);
            $table->dropColumn('colaborador_id');
        });
    }
};
