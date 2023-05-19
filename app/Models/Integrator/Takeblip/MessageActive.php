<?php

namespace App\Models\Integrator\Takeblip;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageActive extends Model
{
    use HasFactory;

    protected $table = 'takeblip_mensagem_ativa';
    protected $fillable = ['cliente', 'numero_original', 'numero_enviado', 'lote', 'vencimento', 'data_envio_whatsapp', 'sucesso'];
    protected $connection = 'mysql_integrator';
}
