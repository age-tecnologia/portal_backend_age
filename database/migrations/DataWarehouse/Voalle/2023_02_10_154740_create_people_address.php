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
        Schema::create('voalle_people_address', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('people_address_id');
            $table->integer('type');
            $table->string('street_type');
            $table->string('postal_code');
            $table->string('street');
            $table->string('number');
            $table->string('address_complement');
            $table->string('neighborhood');
            $table->string('city');
            $table->integer('code_city_id');
            $table->string('state');
            $table->string('country');
            $table->string('code_country');
            $table->string('address_reference');
            $table->string('latitude');
            $table->string('longitude');
            $table->integer('property_type');
            $table->dateTime('created');
            $table->dateTime('modified');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('people_address');
    }
};
