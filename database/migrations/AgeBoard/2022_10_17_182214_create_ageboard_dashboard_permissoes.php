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
        Schema::create('ageboard_dashboard_permissoes', function (Blueprint $table) {
           $table->id();
           $table->unsignedBigInteger('user_id');
           $table->unsignedBigInteger('dashboard_id');
           $table->unsignedBigInteger('permitido_por');
           $table->timestamps();
           $table->softDeletes();

           $table->foreign('user_id')->references('id')->on('portal_users');
           $table->foreign('dashboard_id')->references('id')->on('ageboard_dashboards');
           $table->foreign('permitido_por')->references('id')->on('portal_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ageboard_dashboard_permissoes');
    }
};
