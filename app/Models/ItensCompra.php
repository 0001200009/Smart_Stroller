<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItensCompra extends Model
{
    use HasFactory;

    protected $table = 'itens_compra';
    protected $primaryKey = 'id_item';
    public $timestamps = false;

    protected $fillable = [
        'id_compra',
        'id_produto',
        'quantidade',
        'preco_unitario',
        'subtotal'
    ];

    /**
     * Relacionamento N para 1: Um item refere-se a um produto
     */
    public function produto()
    {
        return $this->belongsTo(Produtos::class, 'id_produto', 'id_produto');
    }

    /**
     * Relacionamento N para 1: Um item pertence a uma compra
     */
    public function compra()
    {
        return $this->belongsTo(Compras::class, 'id_compra', 'id_compra');
    }
}