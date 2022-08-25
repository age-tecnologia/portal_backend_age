<?php

namespace App\Models\AgeRv;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollaboratorMeta extends Model
{
    use HasFactory;

    protected $table = 'agerv_colaboradores_meta';
    protected $fillable = ['colaborador_id', 'mes_competencia', 'meta', 'modified_by'];
    protected $connection = 'mysql';

}
