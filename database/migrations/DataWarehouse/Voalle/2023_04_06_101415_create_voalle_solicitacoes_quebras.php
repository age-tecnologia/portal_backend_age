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
        Schema::create('voalle_requests_breaks', function (Blueprint $table) {
            $table->id();
            $table->string('client_name')->nullable();
            $table->bigInteger('id_contract')->nullable();
            $table->string('stage_contract')->nullable();
            $table->string('status_contract')->nullable();
            $table->dateTime('date_created_contract')->nullable();
            $table->dateTime('date_approval_contract')->nullable();
            $table->string('connection')->nullable();
            $table->string('type_assingment')->nullable();
            $table->string('status_assignment')->nullable();
            $table->string('protocol')->nullable();
            $table->string('team')->nullable();
            $table->string('responsible_name')->nullable();
            $table->string('description')->nullable();
            $table->dateTime('date_beginning_assignment')->nullable();
            $table->dateTime('date_final_assignment')->nullable();
            $table->dateTime('date_beginning_report')->nullable();
            $table->dateTime('date_final_report')->nullable();
            $table->bigInteger('time_report')->nullable();
            $table->string('context')->nullable();
            $table->string('problem')->nullable();
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
        Schema::dropIfExists('voalle_requests_breaks');
    }
};
