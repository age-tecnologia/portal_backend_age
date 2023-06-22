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
        Schema::table('portal_modulos_secoes', function (Blueprint $table) {
            $table->string('cor_fundo')->nullable()->after('icone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('portal_modulos_secoes', function (Blueprint $table) {
            $table->dropColumn('cor_fundo');
        });
    }
};
