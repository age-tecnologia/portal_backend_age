<?php

namespace App\Models\AgeControl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Report extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'agecontrol_relatos';
    protected $fillable = ['condutor_id', 'quilometragem_relatada',
                            'quilometragem_aprovada', 'periodo_id',
                            'nome_foto', 'aprovador_id'
                            ];
}
