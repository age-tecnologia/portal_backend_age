<?php

namespace App\Models\DataWarehouse\Voalle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeopleAddress extends Model
{
    use HasFactory;

    protected $table = 'voalle_people_address';
    protected $connection = 'mysql_datawarehouse';
    protected $fillable = ['people_address_id',
                            'type',
                            'street_type',
                            'postal_code',
                            'street',
                            'number',
                            'address_complement',
                            'neighborhood',
                            'city',
                            'code_city_id',
                            'state',
                            'country',
                            'code_country',
                            'address_reference',
                            'latitude',
                            'longitude',
                            'property_type',
                            'created',
                            'modified',
                            ];
}
