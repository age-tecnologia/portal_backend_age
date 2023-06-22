<?php

namespace App\Models\AgeRv\Collaborator;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collaborator extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'agerv_colaborador';
    protected $fillable = ['voalle_id', 'nome', 'data_admissao', 'user_id', 'funcao_id', 'canal_id', 'tipo_comissao_id', 'coordenador_id', 'gerente_id'];
}
