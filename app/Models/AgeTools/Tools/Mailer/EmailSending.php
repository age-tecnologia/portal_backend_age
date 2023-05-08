<?php

namespace App\Models\AgeTools\Tools\Mailer;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailSending extends Model
{
    use HasFactory;


    protected $table = 'agetools_mailers_emails_enviados';
    protected $fillable = ['lote_id', 'email_destinatario', 'status', 'erro'];
}
