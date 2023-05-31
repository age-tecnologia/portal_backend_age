<?php

namespace App\Models\Portal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModulesGroups extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $table = 'portal_modulos_grupos';
    protected $fillable = ['modulo_id', 'grupo', 'criado_por', 'atualizado_por', 'ativo'];


    public function modules()
    {
        return $this->belongsTo(Modules::class, 'modulo_id');
    }

    public function sections()
    {
        return $this->hasMany(ModulesGroupsSections::class, 'grupo_id');
    }

}
