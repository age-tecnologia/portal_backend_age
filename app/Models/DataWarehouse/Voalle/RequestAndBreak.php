<?php

namespace App\Models\DataWarehouse\Voalle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestAndBreak extends Model
{
    use HasFactory;

    protected $table = 'voalle_requests_breaks';
    protected $connection = 'mysql_datawarehouse';
    protected $fillable = ['id_client', 'client_name', 'id_contract', 'stage_contract', 'status_contract', 'date_created_contract',
                            'date_approval_contract', 'connection', 'type_assignment', 'status_assignment',
                            'protocol', 'team', 'responsible_name', 'description', 'date_beginning_assignment', 'date_final_assignment',
                            'date_beginning_report', 'date_final_report', 'time_report', 'context', 'problem'];
}
