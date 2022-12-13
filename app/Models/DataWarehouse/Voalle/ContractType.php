<?php

namespace App\Models\DataWarehouse\Voalle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractType extends Model
{
    use HasFactory;
    protected $connection = 'mysql_datawarehouse';
    protected $table = 'voalle_contracts_type';
    protected $fillable = ['id_contract_type', 'title'];
}
