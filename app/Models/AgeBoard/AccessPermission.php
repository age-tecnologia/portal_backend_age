<?php

namespace App\Models\AgeBoard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessPermission extends Model
{
    use HasFactory;

    protected $table = 'ageboard_usuarios_permitidos';
    protected $fillable = ['user_id', 'funcao_id', 'setor_id', 'nivel_acesso_id'];
}
