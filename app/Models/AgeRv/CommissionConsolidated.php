<?php

namespace App\Models\AgeRv;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionConsolidated extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $table = 'agerv_comissao_consolidada';
    protected $fillable = ['auditada', 'canal', 'colaborador_id', 'colaborador',
                            'vendas', 'meta', 'meta_atingida', 'vendas_canceladas',
                            'estrelas', 'valor_estrela', 'acelerador_deflator', 'comissao', 'competencia'
                            ];

}
