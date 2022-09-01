<?php

namespace App\Models\AgeRv;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Channel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'agerv_colaboradores_canais';
    protected $fillable = ['canal', 'modified_by'];
    protected $connection = 'mysql';
}
