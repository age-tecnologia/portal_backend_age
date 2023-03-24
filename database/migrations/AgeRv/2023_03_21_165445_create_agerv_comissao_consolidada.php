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
        Schema::create('agerv_comissao_consolidada', function (Blueprint $table) {
            $table->id();
            $table->boolean('auditada');
            $table->string('canal');
            $table->unsignedBigInteger('colaborador_id');
            $table->string('colaborador');
            $table->integer('vendas');
            $table->decimal('meta');
            $table->decimal('meta_atingida');
            $table->integer('vendas_canceladas');
            $table->integer('estrelas');
            $table->decimal('valor_estrela');
            $table->decimal('acelerador_deflator');
            $table->decimal('comissao');
            $table->date('competencia');
            $table->timestamps();
            $table->softDeletes();


            $table->foreign('colaborador_id')->references('id')->on('agerv_colaboradores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agerv_comissao_consolidada');
    }
};
