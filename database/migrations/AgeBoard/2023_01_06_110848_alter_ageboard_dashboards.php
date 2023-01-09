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
        Schema::table('ageboard_dashboards', function (Blueprint $table) {
            $table->boolean('ativo')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ageboard_dashboards', function (Blueprint $table) {
            $table->dropColumn('ativo');
        });
    }
};
