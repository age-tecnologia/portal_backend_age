<?php

namespace App\Models\AgeReport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReportPermission extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'agereport_relatorios_permissoes';
    protected $connection = 'mysql';
    protected $fillable = ['user_id', 'relatorio_id', 'permitido_por'];
}
