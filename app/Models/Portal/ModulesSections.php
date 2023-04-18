<?php

namespace App\Models\Portal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModulesSections extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $table = 'portal_modulos_secoes';
    protected $fillable = ['modulo_id', 'secao', 'icone', 'url', 'ativo'];
}
