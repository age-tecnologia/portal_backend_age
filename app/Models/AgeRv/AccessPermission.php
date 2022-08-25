<?php

namespace App\Models\AgeRv;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessPermission extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'agerv_usuarios_permitidos';
    protected $fillable = ['user_id', 'funcao_id', 'setor_id', 'isAdmin', 'isFinancial'];
    protected $connection = 'mysql';
}
