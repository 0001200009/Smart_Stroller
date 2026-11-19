<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Produtos;
use App\Models\Compras;
use App\Models\ItensCompra;
use App\Models\LeiturasRfid;

class ArduinoController extends Controller
{
    public function registrarLeitura(Request $request)
    {
        $request->validate([
            'codigo_rfid' => 'required|string',
            'id_stroller' => 'required|integer',
            'acao'        => 'required|in:Entrada,Saida'
        ]);

        $produto = Produtos::where('codigo_rfid', $request->codigo_rfid)->first();
        if (!$produto) {
            return response()->json(['erro' => 'Produto nao encontrado'], 404);
        }

        $compra = Compras::where('id_stroller', $request->id_stroller)
                        ->where('status', 'Em Andamento')
                        ->latest('data_inicio')
                        ->first();

        if (!$compra) {
            return response()->json(['erro' => 'Sem compra ativa'], 404);
        }

        LeiturasRfid::create([
            'id_stroller' => $request->id_stroller,
            'id_produto'  => $produto->id_produto,
            'acao'        => $request->acao,
        ]);

        $item = ItensCompra::where('id_compra', $compra->id_compra)
                           ->where('id_produto', $produto->id_produto)
                           ->first();

        if ($request->acao === 'Entrada') {
            if ($item) {
                $item->quantidade += 1;
                $item->subtotal = $item->quantidade * $item->preco_unitario;
                $item->save();
            } else {
                $item = ItensCompra::create([
                    'id_compra'      => $compra->id_compra,
                    'id_produto'     => $produto->id_produto,
                    'quantidade'     => 1,
                    'preco_unitario' => $produto->preco,
                    'subtotal'       => $produto->preco,
                ]);
            }
        } elseif ($request->acao === 'Saida' && $item) {
            if ($item->quantidade > 1) {
                $item->quantidade -= 1;
                $item->subtotal = $item->quantidade * $item->preco_unitario;
                $item->save();
            } else {
                $item->delete();
            }
        }

        $novoTotal = ItensCompra::where('id_compra', $compra->id_compra)->sum('subtotal');
        $compra->update(['valor_total' => $novoTotal]);

        return response()->json([
            'status'  => 'sucesso',
            'produto' => $produto->nome,
            'preco'   => number_format($produto->preco, 2, '.', ''),
            'total'   => $novoTotal
        ], 200);
    }
    public function buscarCarrinho($id_compra){
        $compra = Compras::with('itens.produto')->find($id_compra);

        if (!$compra) {
            return response()->json(['erro' => 'Compra não encontrada'], 404);
        }

        // Mapeia os itens da compra para retornar dados limpos ao JavaScript
        $itens = $compra->itens->map(function($item) {
            return [
                'nome'           => $item->produto ? $item->produto->nome : 'Produto Desconhecido',
                'quantidade'     => $item->quantidade,
                'preco_unitario' => number_format($item->preco_unitario, 2, ',', '.'),
                'subtotal'       => number_format($item->subtotal, 2, ',', '.')
            ];
        });

        return response()->json([
            'itens'       => $itens,
            'valor_total' => number_format($compra->valor_total, 2, ',', '.')
        ]);
    }
}
