<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LevelAccess extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'portal_nivel_acesso';
    protected $fillable = ['nivel'];
    protected $connection = 'mysql';
}
