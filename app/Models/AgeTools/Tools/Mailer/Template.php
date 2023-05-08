<?php

namespace App\Models\AgeTools\Tools\Mailer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $table = 'agetools_mailers_templates';
    protected $fillable = ['nome', 'template', 'mailer_id', 'criado_por', 'modificado_por'];
}
