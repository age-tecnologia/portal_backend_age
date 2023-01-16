<?php

namespace App\Models\AgeControl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AccessPermission extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['user_id', 'funcao_id', 'setor_id', 'nivel_acesso_id'];
    protected $table = 'agecontrol_usuarios_permitidos';
}
