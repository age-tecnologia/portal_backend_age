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
        Schema::table('agecontrol_usuarios_permitidos', function (Blueprint $table) {
           $table->unsignedBigInteger('nivel_acesso_id')->after('setor_id');
           $table->foreign('nivel_acesso_id')->references('id')->on('portal_nivel_acesso');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agecontrol_usuarios_permitidos', function (Blueprint $table) {
            $table->dropForeign(['nivel_acesso_id']);
            $table->dropColumn('nivel_acesso_id');
        });
    }
};
