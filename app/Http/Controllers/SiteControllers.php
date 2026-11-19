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
    public function sistema() {
        return view('sistema');
    }

    public function final() {
        return view('final');
    }

    public function admin() {
        return view('admin.index');
    }

    // TÓPICO 5: Dados em tempo real para o Dashboard do Administrador
    public function dashboard() {
        // 1. Busca a compra que está acontecendo agora no carrinho
        $compraAtiva = Compras::where('status', 'Em Andamento')->latest()->first();

        $itensNoCarrinho = 0;
        $valorTotal = 0.00;

        if ($compraAtiva) {
            $itensNoCarrinho = ItensCompra::where('id_compra', $compraAtiva->id_compra)->sum('quantidade');
            $valorTotal = $compraAtiva->valor_total;
        }

        // 2. Busca o nível da bateria cadastrado no carrinho (ou valor padrão)
        $carrinho = SmartStroller::find(1);
        $nivelBateria = $carrinho ? $carrinho->nivel_bateria : 100;

        return view('admin.dashboard', compact('itensNoCarrinho', 'valorTotal', 'nivelBateria', 'compraAtiva'));
    }

    public function carrinhos() {
        return view('admin.carrinhos');
    }

    public function alertas() {
        return view('admin.alertas');
    }

    public function bemVindo() {
        return view('bemVindo');
    }

    public function excluirProduto($id) {
        LeiturasRfid::where("id_produto", $id)->delete();

        $excluirProduto = Produtos::findOrFail($id);
        $excluirProduto->delete();

        return redirect('/layout');
    }
}