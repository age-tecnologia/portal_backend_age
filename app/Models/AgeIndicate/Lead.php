<?php

namespace App\Models\AgeIndicate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'ageindicate_leads';
    protected $fillable = ['nome_cliente', 'telefone_cliente', 'endereco_cliente'];
}
