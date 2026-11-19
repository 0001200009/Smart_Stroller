<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compras extends Model
{
    use HasFactory;

    protected $table = 'compras';
    protected $primaryKey = 'id_compra';

    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_atualizacao';

    protected $fillable = [
        'id_usuario',
        'id_stroller',
        'valor_total',
        'status',
        'data_inicio',
        'data_fim'
    ];

    /**
     * Relacionamento 1 para N: Uma compra possui vários itens
     */
    public function itens()
    {
        return $this->hasMany(ItensCompra::class, 'id_compra', 'id_compra');
    }

    /**
     * Relacionamento N para 1: Uma compra pertence a um usuário
     */
    public function usuario()
    {
        return $this->belongsTo(Usuarios::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Relacionamento N para 1: Uma compra pertence a um carrinho (stroller)
     */
    public function stroller()
    {
        return $this->belongsTo(SmartStroller::class, 'id_stroller', 'id_stroller');
    }
}