<?php

namespace App\Models\AgeReport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessPermission extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'agereport_usuarios_permitidos';
    protected $connection = 'mysql';
    protected $fillable = ['user_id', 'funcao_id', 'setor_id', 'nivel_acesso_id'];
}
