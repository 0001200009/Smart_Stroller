<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeiturasRfid extends Model
{
    use HasFactory;

    protected $table = 'leituras_rfid';
    protected $primaryKey = 'id_leitura';

    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_atualizacao';

    protected $fillable = [
        'id_stroller',
        'id_produto',
        'acao',
        'data_leitura',
        'data_remocao',
    ];

    public function produto()
    {
        return $this->belongsTo(Produtos::class, 'id_produto', 'id_produto');
    }

    public function stroller()
    {
        return $this->belongsTo(SmartStroller::class, 'id_stroller', 'id_stroller');
    }
}
