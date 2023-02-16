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
        Schema::create('voalle_contract_service_tags', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('contract_id')->nullable();
            $table->string('service_tag')->nullable();
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->bigInteger('client_id')->nullable();
            $table->integer('status')->nullable();
            $table->boolean('active')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voalle_contract_service_tags');
    }
};
