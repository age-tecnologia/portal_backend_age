<?php

namespace App\Models\AgeCommunicate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'agecomunica_modulos';
    protected $fillable = [''];
}
