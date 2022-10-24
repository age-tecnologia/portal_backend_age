<?php

namespace App\Models\AgeRv;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    use HasFactory;

    protected $table = 'agerv_comissao_vendas';
    protected $fillable = ['id_contrato',
                            'mes_competencia',
                            'ano_competencia',
                            'nome_cliente',
                            'status',
                            'situacao',
                            'valor',
                            'data_contrato',
                            'data_ativacao',
                            'conexao',
                            'id_vendedor',
                            'vendedor',
                            'id_supervisor',
                            'supervisor',
                            'data_cancelamento',
                            'plano',
                            'data_vigencia'];
    protected $connection = 'mysql';
}
