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
        Schema::table('agerv_voalle_vendas', function (Blueprint $table) {
            $table->bigInteger('id_vendedor')->after('valor')->nullable();
            $table->bigInteger('id_supervisor')->after('vendedor')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agerv_voalle_vendas', function (Blueprint $table) {
            $table->dropColumn('id_vendedor');
            $table->dropColumn('id_supervisor');
        });
    }
};
