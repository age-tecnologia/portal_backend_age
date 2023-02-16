<?php

namespace App\Models\DataWarehouse\Voalle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractServiceTag extends Model
{
    use HasFactory;
    protected $table = 'voalle_contract_service_tags';
    protected $fillable = ['contract_id', 'service_tag', 'title', 'description', 'client_id', 'status', 'active'];
    protected $connection = 'mysql_datawarehouse';
}
