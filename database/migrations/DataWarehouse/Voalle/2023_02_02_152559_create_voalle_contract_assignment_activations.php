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
        Schema::create('voalle_contract_assignment_activations', function (Blueprint $table) {
            $table->id();
            $table->string('contract_id');
            $table->string('assignment_id');
            $table->string('person_id');
            $table->date('activation_date');
            $table->string('invoice_note_id');
            $table->dateTime('created');
            $table->dateTime('modified');
            $table->string('created_by');
            $table->string('modified_by');
            $table->boolean('deleted');
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
        Schema::dropIfExists('voalle_contract_assignment_activations');
    }
};
