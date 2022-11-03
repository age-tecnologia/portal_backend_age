<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CliqueAssine extends Model
{
    use HasFactory;

    protected $table = 'assine_cliques';
    protected $fillable = ['ip', 'clique_em'];

}
