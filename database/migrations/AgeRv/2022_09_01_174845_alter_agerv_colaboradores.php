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
            $table->unsignedBigInteger('supervisor_id')->after('canal_id')->nullable();
            $table->unsignedBigInteger('gestor_id')->after('supervisor_id')->nullable();
            $table->foreign('supervisor_id')->references('id')->on('agerv_colaboradores');
            $table->foreign('gestor_id')->references('id')->on('portal_users');
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
            $table->dropForeign(['supervisor_id']);
            $table->dropColumn('supervisor_id');
            $table->dropForeign(['gestor_id']);
            $table->dropColumn('gestor_id');
        });
    }
};
