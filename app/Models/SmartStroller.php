<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmartStroller extends Model
{
    use HasFactory;

    protected $table = 'smart_stroller';
    protected $primaryKey = 'id_stroller';

    // Mapeia as colunas de data personalizadas do banco
    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_atualizacao';

    protected $fillable = [
        'numero_identificacao',
        'nivel_bateria',
        'status',
        'ultima_sincronizacao',
        'data_criacao',
        'data_remocao',
        'data_atualizacao',
    ];
}