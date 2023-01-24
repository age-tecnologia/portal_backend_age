<?php

namespace App\Models\AgeControl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleModality extends Model
{
    use HasFactory;

    protected $table = 'agecontrol_veiculo_modalidade';
    protected $fillable = ['modalidade'];
}
