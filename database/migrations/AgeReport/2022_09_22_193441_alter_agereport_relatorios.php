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
           $table->boolean('isPeriodo')->after('url')->default(0);
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
            $table->dropColumn('isPeriodo');
        });
    }
};
