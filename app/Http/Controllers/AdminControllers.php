<?php

namespace App\Http\Controllers;

use App\Models\SmartStroller;
use App\Models\Usuarios;
use App\Models\Produtos;
use App\Models\Compras;
use App\Models\ItensCompra;
use App\Models\Pagamentos;
use App\Models\LeiturasRfid;
use Illuminate\Http\Request; // Corrigido a grafia de Illuminate

class AdminControllers extends Controller
{
    public function layout() {
        $nomeComprador = request()->input('nomeComprador');
        $pedidos = Produtos::all();

        return view('layout', compact('pedidos', 'nomeComprador'));
    }

    public function clientes() {
        $clientes = Usuarios::orderBy('nome')->get();

        return view('admin.clientes', compact('clientes'));
    }

    public function adicionarCliente(Request $request) {
        $salvarCliente = new Usuarios();
        $salvarCliente->nome = $request->input('nome');
        $salvarCliente->email = $request->input('email'); // Adicionado
        $salvarCliente->telefone = $request->input('telefone');
        $salvarCliente->save();

        return redirect('/admin/clientes');
    }

    public function carregarTelaEdicaoCliente($id) {
        $editarCliente = Usuarios::findOrFail($id);
        $clientes = Usuarios::orderBy('nome')->get();

        return view('admin.clientes', compact('editarCliente', 'clientes'));
    }

    public function editarCliente(Request $request, $id) {
        $editarCliente = Usuarios::findOrFail($id);
        $editarCliente->nome = $request->input('nome');
        $editarCliente->email = $request->input('email'); // Adicionado
        $editarCliente->telefone = $request->input('telefone');
        $editarCliente->save();

        return redirect('/admin/clientes');
    }

    public function excluirCliente($id) {
        $comprasIds = Compras::where('id_usuario', $id)->pluck('id_compra');

        ItensCompra::whereIn('id_compra', $comprasIds)->delete();
        Pagamentos::whereIn('id_compra', $comprasIds)->delete();
        Compras::where('id_usuario', $id)->delete();

        $cliente = Usuarios::findOrFail($id);
        $cliente->delete();

        return redirect('/admin/clientes');
    }

    public function compras() {
        $compras = Compras::orderBy('data_criacao', 'desc')->get();

        return view('admin.compras', compact('compras'));
    }

    public function adicionarCompra(Request $request) {
        $salvarCompra = new Compras();
        $salvarCompra->id_usuario = $request->input('id_usuario');
        $salvarCompra->id_stroller = $request->input('id_stroller', 1);
        $salvarCompra->valor_total = $request->input('valor_total', 0);
        $salvarCompra->status = $request->input('status', 'Em Andamento');
        $salvarCompra->save();

        return redirect('/admin/compras');
    }

    public function carregarTelaEdicaoCompra($id) {
        $editarCompra = Compras::findOrFail($id);
        $compras = Compras::orderBy('data_criacao', 'desc')->get();

        return view('admin.compras', compact('editarCompra', 'compras'));
    }

    public function editarCompra(Request $request, $id) {
        $editarCompra = Compras::findOrFail($id);
        $editarCompra->id_usuario = $request->input('id_usuario');
        $editarCompra->id_stroller = $request->input('id_stroller');
        $editarCompra->valor_total = $request->input('valor_total');
        $editarCompra->status = $request->input('status');
        $editarCompra->save();

        return redirect('/admin/compras');
    }

    public function excluirCompra($id) {
        ItensCompra::where("id_compra", $id)->delete();
        Pagamentos::where("id_compra", $id)->delete();

        $excluirCompra = Compras::findOrFail($id);
        $excluirCompra->delete();

        return redirect('/admin/compras');
    }

    public function produtos() {
        $produtos = Produtos::orderBy('nome')->get();

        return view('admin.produtos', compact('produtos'));
    }

    public function salvarProduto(Request $request) {
        $salvarProduto = new Produtos();
        $salvarProduto->nome = $request->input('nome');
        $salvarProduto->preco = $request->input('preco');
        $salvarProduto->codigo_rfid = $request->input('codigo_rfid');
        $salvarProduto->estoque = $request->input('estoque');
        $salvarProduto->id_categoria = $request->input('categoria');
        $salvarProduto->status = $request->input('status', 'disponivel');
        $salvarProduto->save();

        return redirect('/admin/produtos');
    }

    public function carregarTelaEdicao($id) {
        $editarProduto = Produtos::findOrFail($id);
        $produtos = Produtos::all();

        return view('admin.produtos', compact('editarProduto', 'produtos'));
    }

    public function editarProduto(Request $request, $id) {
        $editarProduto = Produtos::findOrFail($id);
        $editarProduto->nome = $request->input('nome');
        $editarProduto->preco = $request->input('preco');
        $editarProduto->codigo_rfid = $request->input('codigo_rfid');
        $editarProduto->estoque = $request->input('estoque');
        $editarProduto->status = $request->input('status');
        $editarProduto->save();

        return redirect('/admin/produtos');
    }

    public function excluirProduto($id) {
        LeiturasRfid::where("id_produto", $id)->delete();
        ItensCompra::where("id_produto", $id)->delete();

        $excluirProduto = Produtos::findOrFail($id);
        $excluirProduto->delete(); 

        return redirect()->back();
    }

    public function login() {
        return view('admin.login');
    }
    public function salvarStroller(Request $request) {
        $request->validate([
            'numero_identificacao' => 'required|unique:smart_stroller,numero_identificacao',
            'nivel_bateria'        => 'nullable|integer|min:0|max:100',
            'status'               => 'required|in:Disponivel,Em Uso,Pagamento,Manutencao'
        ]);

        SmartStroller::create([
            'numero_identificacao' => $request->input('numero_identificacao'),
            'nivel_bateria'        => $request->input('nivel_bateria', 100),
            'status'               => $request->input('status', 'Disponivel'),
            'ultima_sincronizacao' => now(),
        ]);

        return redirect()->back()->with('sucesso', 'Smart Stroller cadastrado com sucesso!');
    }
}