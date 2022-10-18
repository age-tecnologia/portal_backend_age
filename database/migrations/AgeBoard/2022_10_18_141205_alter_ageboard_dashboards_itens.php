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
        Schema::table('ageboard_dashboards_itens', function (Blueprint $table) {
            $table->unsignedBigInteger('dashboard_id')->after('id');

            $table->foreign('dashboard_id')->references('id')->on('ageboard_dashboards');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ageboard_dashboards_itens', function (Blueprint $table) {
            $table->dropColumn('dashboard_id');
        });
    }
};
