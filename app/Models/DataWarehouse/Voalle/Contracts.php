<?php

namespace App\Models\DataWarehouse\Voalle;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contracts extends Model
{
    use HasFactory;
    protected $connection = 'mysql_datawarehouse';
    protected $table = 'voalle_contracts';
    protected $fillable = ['id_contract',
                            'client_id',
                            'contract_number',
                            'description',
                            'contract_type_id',
                            'date',
                            'beginning_date',
                            'final_date',
                            'billing_beginning_date',
                            'billing_final_date',
                            'collection_day',
                            'cut_day',
                            'seller_1_id',
                            'seller_2_id',
                            'amount',
                            'status',
                            'stage',
                            'cancellation_date',
                            'cancellation_motive',
                            'approval_submission_date',
                            'approval_date',
                            'v_stage',
                            'v_status',
                            'v_invoice_type',
                            'people_address_id'];
}
