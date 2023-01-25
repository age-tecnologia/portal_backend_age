<?php

namespace App\Models\AgeControl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportPeriod extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $table = 'agecontrol_relato_periodos';
    protected $fillable = ['ordem', 'periodo'];
}
