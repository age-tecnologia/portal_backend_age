<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SystemPermission extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'permissoes_sistema';
    protected $connection = 'mysql';


}
