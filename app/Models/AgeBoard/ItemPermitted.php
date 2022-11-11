<?php

namespace App\Models\AgeBoard;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemPermitted extends Model
{
    use HasFactory;

    protected $table = 'ageboard_dashboards_itens_permissoes';
    protected $fillable = ['user_id', 'dashboard_id', 'item_id', 'criado_por', 'modificado_por'];

}
