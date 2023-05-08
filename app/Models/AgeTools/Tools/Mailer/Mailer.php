<?php

namespace App\Models\AgeTools\Tools\Mailer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mailer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'agetools_mailers';
    protected $fillable = ['mailer', 'configuracao', 'limite_diario', 'criado_por', 'modificado_por'];




}
