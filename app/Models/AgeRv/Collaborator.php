<?php

namespace App\Models\AgeRv;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Collaborator extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'agerv_colaboradores';

    protected $fillable = [ 'nome',
                            'user_id',
                            'funcao_id',
                            'tipo_comissao_id',
                            'supervisor_id',
                            'gestor_id',
                            'canal_id'
                            ];

    protected $connection = 'mysql';
}
