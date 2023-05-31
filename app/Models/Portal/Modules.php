<?php

namespace App\Models\Portal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modules extends Model
{
    use HasFactory;


    protected $table = 'portal_modulos';
    protected $fillable = ['modulo', 'icone', 'descricao', 'rota', 'ativo'];


    public function sections()
    {
        return $this->hasMany(ModulesSections::class, 'modulo_id')
                ->whereAtivo(1)
                ->select('id', 'secao', 'url', 'icone', 'modulo_id');

    }


}
