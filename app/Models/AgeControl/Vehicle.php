<?php

namespace App\Models\AgeControl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    protected $table = 'agecontrol_veiculos';
    protected $fillable = [
                        'condutor_id',
                        'tipo_veiculo_id',
                        'fabricante',
                        'modelo',
                        'capacidade_tanque',
                        'media_km_litro',
                        'quilometragem_inicial',
                        'distancia_sede_casa',
                        'modalidade_id',
                    ];
}
