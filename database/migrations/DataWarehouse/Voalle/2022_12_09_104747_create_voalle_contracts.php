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
        Schema::create('voalle_contracts', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_contract');
            $table->integer('client_id');
            $table->string('contract_number');
            $table->text('description');
            $table->integer('contract_type_id');
            $table->date('date');
            $table->date('beginning_date');
            $table->date('final_date');
            $table->date('billing_beginning_date');
            $table->date('billing_final_date');
            $table->integer('collection_day');
            $table->integer('cut_day');
            $table->integer('seller_1_id');
            $table->integer('seller_2_id');
            $table->float('amount');
            $table->integer('status');
            $table->integer('stage');
            $table->date('cancellation_date');
            $table->text('cancellation_motive');
            $table->date('approval_submission_date');
            $table->date('approval_date');
            $table->string('v_stage');
            $table->string('v_status');
            $table->string('v_invoice_type');
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
        Schema::dropIfExists('voalle_contracts');
    }
};
