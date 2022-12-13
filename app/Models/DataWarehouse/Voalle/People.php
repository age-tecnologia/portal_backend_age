<?php

namespace App\Models\DataWarehouse\Voalle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    use HasFactory;
    protected $connection = 'mysql_datawarehouse';
    protected $table = 'voalle_peoples';
    protected $fillable = ['id_people', 'name', 'street', 'neighborhood',
                            'city', 'postal_code'];
}
