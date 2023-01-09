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
        Schema::table('voalle_peoples', function (Blueprint $table) {
            $table->integer('type_tx_id')->after('name');
            $table->string('tx_id')->after('type_tx_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('voalle_peoples', function (Blueprint $table) {
            $table->dropColumn('type_tx_id');
            $table->dropColumn('tx_id');

        });
    }
};
