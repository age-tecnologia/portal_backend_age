<?php

namespace App\Models\AgeRv;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoalleSales extends Model
{
    use HasFactory;

    protected $table = 'agerv_voalle_vendas';
    protected $fillable = ['id_contrato',
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
