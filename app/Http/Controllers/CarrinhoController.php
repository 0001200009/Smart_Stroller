<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compras;

class CarrinhoController extends Controller
{
    public function obterDadosCarrinho($id_compra)
    {
        $compra = Compras::with('itens.produto')->find($id_compra);

        if (!$compra) {
            return response()->json(['itens' => [], 'valor_total' => '0,00']);
        }

        return response()->json([
            'itens' => $compra->itens,
            'valor_total' => number_format($compra->valor_total, 2, ',', '.')
        ]);
    }
}
