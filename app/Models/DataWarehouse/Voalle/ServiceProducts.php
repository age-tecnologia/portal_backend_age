<?php

namespace App\Models\DataWarehouse\Voalle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProducts extends Model
{
    use HasFactory;
    protected $connection = 'mysql_datawarehouse';
    protected $table = 'voalle_service_products';
    protected $fillable = ['id_service_product', 'title'];
}
