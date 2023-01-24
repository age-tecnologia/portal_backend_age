<?php

namespace App\Models\AgeControl;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory;

    protected $table = 'agecontrol_servicos';
    protected $fillable = ['servico'];

}
