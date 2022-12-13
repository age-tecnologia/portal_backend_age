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
        Schema::create('voalle_authentication_contracts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_authentication_contract');
            $table->bigInteger('contract_id');
            $table->bigInteger('service_product_id');
            $table->string('user');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('voalle_authentication_contracts');
    }
};
