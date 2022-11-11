<?php

namespace App\Models\AgeBoard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardPermitted extends Model
{
    use HasFactory;

    protected $table = 'ageboard_dashboard_permissoes';
    protected $fillable = ['user_id', 'dashboard_id', 'permitido_por'];

}
