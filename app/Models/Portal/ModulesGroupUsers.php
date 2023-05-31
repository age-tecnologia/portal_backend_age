<?php

namespace App\Models\Portal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModulesGroupUsers extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'portal_modulos_grupos_usuarios';
    protected $fillable = ['user_id', 'grupo_id', 'adicionado_por', 'atualizado_por'];


    public function groups()
    {
        return $this->hasOne(ModulesGroups::class, 'id', 'grupo_id')->select('id', 'modulo_id', 'grupo');
    }
}
