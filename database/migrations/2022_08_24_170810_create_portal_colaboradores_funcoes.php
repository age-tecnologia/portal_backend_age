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
        Schema::create('portal_colaboradores_funcoes', function (Blueprint $table) {
            $table->id();
            $table->string('funcao');
            $table->unsignedBigInteger('modified_by');
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('portal_colaboradores_funcoes');
    }
};
