<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagamentos extends Model
{
    use HasFactory;

    protected $table = 'pagamentos';
    protected $primaryKey = 'id_pagamento';

    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_atualizacao';

    protected $fillable = [
        'id_compra',
        'metodo',
        'valor',
        'status',
        'data_pagamento',
        'data_remocao',
    ];

    public function compra()
    {
        return $this->belongsTo(Compras::class, 'id_compra', 'id_compra');
    }
}
