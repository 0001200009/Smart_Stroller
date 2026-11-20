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

        $codigo = trim($request->codigo_rfid);

        $produto = Produtos::where('codigo_rfid', $codigo)
            ->orWhere('codigo_barras', $codigo)
            ->first();

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

        $novoTotal = (float) ItensCompra::where('id_compra', $compra->id_compra)->sum('subtotal');
        $compra->update(['valor_total' => $novoTotal]);

        $impostosAprox = round($novoTotal * 0.185, 2);
        $quantidadeTotal = (int) ItensCompra::where('id_compra', $compra->id_compra)->sum('quantidade');

        return response()->json([
            'status'           => 'sucesso',
            'produto'          => $produto->nome,
            'preco'            => number_format((float) $produto->preco, 2, '.', ''),
            'quantidade_total' => $quantidadeTotal,
            'impostos'         => number_format($impostosAprox, 2, '.', ''),
            'total'            => number_format($novoTotal, 2, '.', '')
        ], 200);
    }

    public function buscarCarrinho($id_compra)
    {
        $compra = Compras::with('itens.produto')->find($id_compra);

        if (!$compra) {
            return response()->json(['erro' => 'Compra não encontrada'], 404);
        }

        $quantidadeTotal = 0;
        $itens = $compra->itens->map(function ($item) use (&$quantidadeTotal) {
            $quantidadeTotal += (int) $item->quantidade;
            $precoUnitario = (float) $item->preco_unitario;
            $subtotal = (float) $item->subtotal;
            $impostoItem = round($subtotal * 0.185, 2);

            return [
                'id_produto'     => $item->id_produto,
                'nome'           => $item->produto ? $item->produto->nome : 'Produto Desconhecido',
                'codigo_rfid'    => $item->produto ? $item->produto->codigo_rfid : null,
                'codigo_barras'  => $item->produto ? $item->produto->codigo_barras : null,
                'quantidade'     => (int) $item->quantidade,
                'preco_unitario' => number_format($precoUnitario, 2, ',', '.'),
                'imposto_aprox'  => number_format($impostoItem, 2, ',', '.'),
                'subtotal'       => number_format($subtotal, 2, ',', '.')
            ];
        });

        $valorTotal = (float) $compra->valor_total;
        $impostosTotal = round($valorTotal * 0.185, 2);
        $subtotalLiquido = max(0, $valorTotal - $impostosTotal);

        return response()->json([
            'itens'            => $itens,
            'quantidade_total' => $quantidadeTotal,
            'impostos_total'   => number_format($impostosTotal, 2, ',', '.'),
            'subtotal_liquido' => number_format($subtotalLiquido, 2, ',', '.'),
            'valor_total'      => number_format($valorTotal, 2, ',', '.')
        ]);
    }
}
