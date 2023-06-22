<?php

namespace App\Models\Portal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modules extends Model
{
    use HasFactory;


    protected $table = 'portal_modulos';
    protected $fillable = ['modulo', 'icone', 'cor_fundo', 'descricao', 'rota', 'ativo'];


    public function sections()
    {
        return $this->hasMany(ModulesSections::class, 'modulo_id')
                ->whereAtivo(1)
                ->orderBy('ordernacao')
                ->select('id', 'secao', 'url', 'icone', 'cor_fundo', 'modulo_id', 'descricao');

    }


}
