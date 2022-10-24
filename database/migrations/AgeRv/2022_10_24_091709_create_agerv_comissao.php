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
        Schema::create('agerv_comissao_vendas', function (Blueprint $table) {
            $table->id();
            $table->string('mes_competencia');
            $table->string('ano_competencia');
            $table->string('id_contrato')->nullable();
            $table->string('nome_cliente')->nullable();
            $table->string('status')->nullable();
            $table->string('situacao')->nullable();
            $table->date('data_contrato')->nullable();
            $table->date('data_ativacao')->nullable();
            $table->date('data_vigencia')->nullable();
            $table->string('conexao')->nullable();
            $table->double('valor')->nullable();
            $table->string('vendedor')->nullable();
            $table->string('supervisor')->nullable();
            $table->date('data_cancelamento')->nullable();
            $table->string('plano')->nullable();
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
        Schema::dropIfExists('agerv_comissao');
    }
};
