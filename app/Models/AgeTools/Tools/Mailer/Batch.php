<?php

namespace App\Models\AgeTools\Tools\Mailer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $table = 'agetools_mailers_lotes_enviados';
    protected $fillable = ['mailer_id', 'template_id', 'enviado_por'];
}
