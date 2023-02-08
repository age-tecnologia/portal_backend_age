<?php

namespace App\Models\DataWarehouse\Voalle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractAssignmentActivations extends Model
{
    use HasFactory;

    protected $table = 'voalle_contract_assignment_activations';
    protected $connection = 'mysql_datawarehouse';
    protected $fillable = ['contract_id', 'assignment_id', 'person_id', 'activation_date',
                            'invoice_note_id', 'created', 'modified', 'created_by', 'modified_by', 'deleted'];
}
