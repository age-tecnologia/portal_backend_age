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
        Schema::create('portal_sistema_permissoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('sistema_id');
            $table->unsignedBigInteger('modified_by');
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('user_id')->references('id')->on('portal_users');
            $table->foreign('sistema_id')->references('id')->on('portal_sistemas');
            $table->foreign('modified_by')->references('id')->on('portal_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('portal_sistema_permissoes');
    }
};
