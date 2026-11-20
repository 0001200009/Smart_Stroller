<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Compras;
use App\Models\ItensCompra;
use App\Models\Produtos;
use App\Models\SmartStroller;
use App\Models\LeiturasRfid;

class SiteControllers extends Controller
{
    public function sistema()
    {
        return view('sistema');
    }

    public function final()
    {
        return view('final');
    }

    public function bemVindo()
    {
        return view('bemVindo');
    }

    /**
     * Dashboard do Administrador com métricas operacionais e financeiras em tempo real
     */
    /**
     * Dashboard do Administrador com métricas operacionais e financeiras em tempo real
     */
    public function dashboard(\Illuminate\Http\Request $request)
    {
        // 1. Busca todos os carrinhos para popular o menu de seleção
        $carrinhos = SmartStroller::all();

        // 2. Identifica o carrinho selecionado na URL (ou pega o primeiro por padrão)
        $strollerId = $request->input('stroller_id', $carrinhos->first()->id_stroller ?? 1);
        $carrinhoAtual = SmartStroller::find($strollerId);

        // 3. Busca a compra ativa SOMENTE do carrinho selecionado
        $compraAtiva = Compras::with(['usuario', 'itens.produto', 'stroller'])
            ->where('status', 'Em Andamento')
            ->where('id_stroller', $strollerId)
            ->latest('data_inicio')
            ->first();

        $itensNoCarrinho = 0;
        $valorTotal = 0.00;

        if ($compraAtiva) {
            $itensNoCarrinho = (int) ItensCompra::where('id_compra', $compraAtiva->id_compra)->sum('quantidade');
            $valorTotal = (float) $compraAtiva->valor_total;
        }

        // 4. Telemetria do carrinho selecionado
        $nivelBateria = $carrinhoAtual ? $carrinhoAtual->nivel_bateria : 100;
        $statusCarrinho = $carrinhoAtual ? $carrinhoAtual->status : 'Disponivel';

        // 5. Indicadores de Performance (ROI e Operação) globais da loja
        $totalVendasHoje = (float) Compras::where('status', 'Concluida')
            ->whereDate('data_criacao', now()->toDateString())
            ->sum('valor_total');

        $comprasConcluidasHoje = Compras::where('status', 'Concluida')
            ->whereDate('data_criacao', now()->toDateString())
            ->count();

        $totalProdutos = Produtos::count();
        $totalCarrinhos = SmartStroller::count();
        $produtosEmFaltaCount = Produtos::where('status', 'em falta')->count();

        // 6. Retorna a view enviando as novas variáveis ($carrinhos e $carrinhoAtual)
        return view('admin.dashboard', compact(
            'carrinhos',
            'carrinhoAtual',
            'itensNoCarrinho',
            'valorTotal',
            'nivelBateria',
            'statusCarrinho',
            'compraAtiva',
            'totalVendasHoje',
            'comprasConcluidasHoje',
            'totalProdutos',
            'totalCarrinhos',
            'produtosEmFaltaCount'
        ));
    }

    public function carrinhos()
    {
        $carrinhos = SmartStroller::all();
        return view('admin.carrinhos', compact('carrinhos'));
    }

    /**
     * Central de Alertas e Quebras Operacionais (Segurança Antifraude e Estoque)
     */
    public function alertas()
    {
        $produtosEmFalta = Produtos::where('status', 'em falta')->get();
        $produtosEstoqueBaixo = Produtos::where('estoque', '<', 20)->where('status', '!=', 'em falta')->get();
        $carrinhosAlerta = SmartStroller::where('nivel_bateria', '<', 20)
            ->orWhere('status', 'Manutencao')
            ->get();

        return view('admin.alertas', compact('produtosEmFalta', 'produtosEstoqueBaixo', 'carrinhosAlerta'));
    }

    public function excluirProduto($id)
    {
        LeiturasRfid::where("id_produto", $id)->delete();
        ItensCompra::where("id_produto", $id)->delete();

        $excluirProduto = Produtos::findOrFail($id);
        $excluirProduto->delete();

        return redirect('/layout');
    }
}
