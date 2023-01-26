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
        Schema::table('agecontrol_relatos', function (Blueprint $table){
           $table->date('data_referencia')->after('quilometragem_aprovada')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('agecontrol_relatos', function (Blueprint $table){
            $table->dropColumn('data_referencia');
        });
    }
};
