<?php

namespace App\Models\Portal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModulesGroupsSections extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $table = 'portal_modulos_grupos_secoes';

    protected $fillable = ['grupo_id', 'secao_id', 'criado_por', 'atualizado_por', 'ativo'];

    public function groups()
    {
        return $this->belongsTo(ModulesGroups::class, 'grupo_id');
    }

    public function section()
    {
        return $this->belongsTo(ModulesSections::class, 'secao_id');
    }

}
