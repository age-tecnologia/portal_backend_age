<?php

namespace App\Models\DataWarehouse\Voalle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    use HasFactory;
    protected $connection = 'mysql_datawarehouse';
    protected $table = 'voalle_peoples';
    protected $fillable = ['id_people', 'birth_date', 'gender', 'type_tx_id', 'email', 'tel',
                        'tx_id', 'name', 'street', 'neighborhood',
                            'city', 'postal_code'];
}
