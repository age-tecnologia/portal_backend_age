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
        Schema::table('agerv_usuarios_permitidos', function (Blueprint $table) {
            $table->boolean('isManager')->after('isAdmin');
            $table->boolean('isSupervisor')->after('isManager');
            $table->boolean('isSeller')->after('isSupervisor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agerv_usuarios_permitidos', function (Blueprint $table) {
            $table->dropColumn('isManager');
            $table->dropColumn('isSupervisor');
            $table->dropColumn('isSeller');
        });
    }
};
