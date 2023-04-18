<?php

namespace App\Models\Portal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modules extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $table = 'portal_modulos';
    protected $fillable = ['modulo', 'icone', 'descricao', 'ativo'];
}
