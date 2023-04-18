<?php

namespace App\Models\Portal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModulesLogs extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'portal_modulos_log';
    protected $fillable = ['modulo_id', 'user_id', 'erro', 'log'];

}
