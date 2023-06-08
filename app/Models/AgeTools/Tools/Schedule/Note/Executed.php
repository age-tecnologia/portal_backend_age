<?php

namespace App\Models\AgeTools\Tools\Schedule\Note;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Executed extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['executada_por', 'protocolo', 'data_inicio_atendimento', 'data_fim_atendimento',
                            'data_inicio_agendamento', 'data_fim_agendamento'];
    protected $table = 'agetools_agenda_executadas';
}
