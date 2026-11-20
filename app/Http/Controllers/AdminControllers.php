<?php

namespace App\Http\Controllers;

use App\Models\SmartStroller;
use App\Models\Usuarios;
use App\Models\Produtos;
use App\Models\Categorias;
use App\Models\Compras;
use App\Models\ItensCompra;
use App\Models\Pagamentos;
use App\Models\LeiturasRfid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AdminControllers extends Controller
{
    public function layout()
    {
        $nomeComprador = request()->input('nomeComprador');
        $pedidos = Produtos::all();

        return view('layout', compact('pedidos', 'nomeComprador'));
    }

    public function clientes()
    {
        $clientes = Usuarios::orderBy('nome')->get();

        return view('admin.clientes', compact('clientes'));
    }

    public function adicionarCliente(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'cpf'  => 'required|string|max:20',
        ]);

        $salvarCliente = new Usuarios();
        $salvarCliente->nome = trim($request->input('nome'));
        $salvarCliente->cpf = trim($request->input('cpf'));
        $salvarCliente->email = $request->input('email');
        $salvarCliente->telefone = $request->input('telefone');
        $salvarCliente->save();

        return redirect('/admin/clientes')->with('sucesso', 'Cliente cadastrado com sucesso!');
    }

    public function carregarTelaEdicaoCliente($id)
    {
        $editarCliente = Usuarios::findOrFail($id);
        $clientes = Usuarios::orderBy('nome')->get();

        return view('admin.clientes', compact('editarCliente', 'clientes'));
    }

    public function editarCliente(Request $request, $id)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'cpf'  => 'required|string|max:20',
        ]);

        $editarCliente = Usuarios::findOrFail($id);
        $editarCliente->nome = trim($request->input('nome'));
        $editarCliente->cpf = trim($request->input('cpf'));
        $editarCliente->email = $request->input('email');
        $editarCliente->telefone = $request->input('telefone');
        $editarCliente->save();

        return redirect('/admin/clientes')->with('sucesso', 'Cliente atualizado com sucesso!');
    }

    public function excluirCliente($id)
    {
        $comprasIds = Compras::where('id_usuario', $id)->pluck('id_compra');

        ItensCompra::whereIn('id_compra', $comprasIds)->delete();
        Pagamentos::whereIn('id_compra', $comprasIds)->delete();
        Compras::where('id_usuario', $id)->delete();

        $cliente = Usuarios::findOrFail($id);
        $cliente->delete();

        return redirect('/admin/clientes')->with('sucesso', 'Cliente removido com sucesso!');
    }

    public function compras()
    {
        $compras = Compras::with(['usuario', 'stroller'])->orderBy('data_criacao', 'desc')->get();
        $usuarios = Usuarios::orderBy('nome')->get();
        $strollers = SmartStroller::all();

        return view('admin.compras', compact('compras', 'usuarios', 'strollers'));
    }

    public function adicionarCompra(Request $request)
    {
        $salvarCompra = new Compras();
        $salvarCompra->id_usuario = $request->input('id_usuario');
        $salvarCompra->id_stroller = $request->input('id_stroller', 1);
        $salvarCompra->valor_total = $request->input('valor_total', 0);
        $salvarCompra->status = $request->input('status', 'Em Andamento');
        $salvarCompra->data_inicio = now();
        $salvarCompra->save();

        return redirect('/admin/compras')->with('sucesso', 'Compra criada com sucesso!');
    }

    public function carregarTelaEdicaoCompra($id)
    {
        $editarCompra = Compras::findOrFail($id);
        $compras = Compras::with(['usuario', 'stroller'])->orderBy('data_criacao', 'desc')->get();
        $usuarios = Usuarios::orderBy('nome')->get();
        $strollers = SmartStroller::all();

        return view('admin.compras', compact('editarCompra', 'compras', 'usuarios', 'strollers'));
    }

    public function editarCompra(Request $request, $id)
    {
        $editarCompra = Compras::findOrFail($id);
        $editarCompra->id_usuario = $request->input('id_usuario');
        $editarCompra->id_stroller = $request->input('id_stroller');
        $editarCompra->valor_total = $request->input('valor_total');
        $editarCompra->status = $request->input('status');
        $editarCompra->save();

        return redirect('/admin/compras')->with('sucesso', 'Compra atualizada com sucesso!');
    }

    public function excluirCompra($id)
    {
        ItensCompra::where("id_compra", $id)->delete();
        Pagamentos::where("id_compra", $id)->delete();

        $excluirCompra = Compras::findOrFail($id);
        $excluirCompra->delete();

        return redirect('/admin/compras')->with('sucesso', 'Compra excluída com sucesso!');
    }

    public function produtos()
    {
        $produtos = Produtos::with('categoria')->orderBy('nome')->get();
        $categorias = Categorias::orderBy('nome')->get();

        return view('admin.produtos', compact('produtos', 'categorias'));
    }

    public function salvarProduto(Request $request)
    {
        $request->validate([
            'nome'        => 'required|string|max:150',
            'preco'       => 'required|numeric|min:0',
            'codigo_rfid' => 'required|string|max:100|unique:produtos,codigo_rfid',
        ]);

        $salvarProduto = new Produtos();
        $salvarProduto->nome = trim($request->input('nome'));
        $salvarProduto->preco = $request->input('preco');
        $salvarProduto->codigo_rfid = trim($request->input('codigo_rfid'));
        $salvarProduto->codigo_barras = $request->input('codigo_barras');
        $salvarProduto->estoque = $request->input('estoque', 0);
        $salvarProduto->id_categoria = $request->input('categoria');
        $salvarProduto->status = $request->input('status', 'disponivel');
        $salvarProduto->save();

        return redirect('/admin/produtos')->with('sucesso', 'Produto adicionado com sucesso!');
    }

    public function carregarTelaEdicao($id)
    {
        $editarProduto = Produtos::findOrFail($id);
        $produtos = Produtos::with('categoria')->orderBy('nome')->get();
        $categorias = Categorias::orderBy('nome')->get();

        return view('admin.produtos', compact('editarProduto', 'produtos', 'categorias'));
    }

    public function editarProduto(Request $request, $id)
    {
        $request->validate([
            'nome'        => 'required|string|max:150',
            'preco'       => 'required|numeric|min:0',
            'codigo_rfid' => 'required|string|max:100',
        ]);

        $editarProduto = Produtos::findOrFail($id);
        $editarProduto->nome = trim($request->input('nome'));
        $editarProduto->preco = $request->input('preco');
        $editarProduto->codigo_rfid = trim($request->input('codigo_rfid'));
        $editarProduto->codigo_barras = $request->input('codigo_barras');
        $editarProduto->estoque = $request->input('estoque');
        $editarProduto->id_categoria = $request->input('categoria');
        $editarProduto->status = $request->input('status');
        $editarProduto->save();

        return redirect('/admin/produtos')->with('sucesso', 'Produto atualizado com sucesso!');
    }

    public function excluirProduto($id)
    {
        LeiturasRfid::where("id_produto", $id)->delete();
        ItensCompra::where("id_produto", $id)->delete();

        $excluirProduto = Produtos::findOrFail($id);
        $excluirProduto->delete();

        return redirect()->back()->with('sucesso', 'Produto excluído com sucesso!');
    }

    public function login()
    {
        return view('admin.login');
    }

    public function autenticar(Request $request)
    {
        // 1. Coleta os dados do formulário
        $login = $request->input('login');
        $senha = $request->input('senha');

        // 2. Verifica as credenciais no banco de dados (Tabela que criamos)
        // OBS: Se você gravou a senha em texto puro, a comparação é direta.
        // Se usou Hash::make() do Laravel na hora de gravar, use Hash::check()
        $admin = DB::table('administradores')
            ->where('login', $login)
            ->where('senha', $senha)
            ->first();

        // 3. Redireciona de acordo com o resultado
        if ($admin) {
            // Se as credenciais estiverem corretas, redireciona para a rota do dashboard
            return redirect()->route('admin.dashboard');
        } else {
            // Se estiverem erradas, volta para a tela de login com uma mensagem de erro
            return back()->with('erro', 'Login ou senha incorretos.');
        }
    }

    public function logout()
    {
        session()->forget('admin_autenticado');
        return redirect('/login');
    }

    public function salvarStroller(Request $request)
    {
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
