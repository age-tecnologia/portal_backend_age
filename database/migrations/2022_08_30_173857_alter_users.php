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
        Schema::table('portal_users', function (Blueprint $table) {
           $table->dropColumn('isAdmin');
           $table->dropColumn('isMaster');
           $table->dropColumn('isCommittee');
           $table->unsignedBigInteger('nivel_acesso_id')->default(1)->after('email');
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
        Schema::table('portal_users', function (Blueprint $table) {
            $table->boolean('isMaster')->after('email');
            $table->boolean('isAdmin')->after('isMaster');
            $table->boolean('isCommittee')->after('isAdmin');
            $table->dropForeign(['nivel_acesso_id']);
            $table->dropColumn('nivel_acesso_id');
        });
    }
};
