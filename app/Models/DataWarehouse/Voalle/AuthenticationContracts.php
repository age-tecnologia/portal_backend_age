<?php

namespace App\Models\DataWarehouse\Voalle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthenticationContracts extends Model
{
    use HasFactory;
    protected $connection = 'mysql_datawarehouse';
    protected $table = 'voalle_authentication_contracts';
    protected $fillable = ['id_authentication_contract', 'contract_id', 'service_product_id', 'user'];

}
