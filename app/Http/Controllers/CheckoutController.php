<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuarios;
use App\Models\Compras;
use App\Models\Produtos;
use App\Models\SmartStroller;

class CheckoutController extends Controller
{
    /**
     * Inicia a sessão de compra vinculando cliente existente (Login) ou novo cliente (Cadastro)
     */
    public function iniciarCompra(Request $request)
    {
        $tipoAcesso = $request->input('tipo_acesso', 'login');

        if ($tipoAcesso === 'login') {
            // Cliente já cadastrado: Nome e CPF
            $request->validate([
                'nome' => 'required|string|max:100',
                'cpf'  => 'required|string|max:20',
            ], [
                'nome.required' => 'Por favor, informe seu nome.',
                'cpf.required'  => 'Por favor, informe seu CPF.',
            ]);

            $cpfLimpo = preg_replace('/[^0-9]/', '', $request->cpf);
            $nomeDigitado = trim($request->nome);

            // Busca o cliente pelo CPF (com ou sem formatação)
            $usuario = Usuarios::where(function($q) use ($request, $cpfLimpo) {
                $q->where('cpf', $request->cpf);
                if (!empty($cpfLimpo)) {
                    $q->orWhere('cpf', $cpfLimpo);
                }
            })->first();

            if (!$usuario) {
                return redirect()->back()
                    ->withInput()
                    ->with('aba_ativa', 'login')
                    ->with('erro', 'CPF não encontrado. Caso ainda não tenha cadastro, acesse a aba "Novo Cliente".');
            }

            // Verifica se o nome confere de forma flexível (case-insensitive)
            $nomeCadastrado = trim($usuario->nome);
            if (mb_strtolower($nomeCadastrado, 'UTF-8') !== mb_strtolower($nomeDigitado, 'UTF-8') &&
                stripos($nomeCadastrado, $nomeDigitado) === false &&
                stripos($nomeDigitado, $nomeCadastrado) === false) {
                return redirect()->back()
                    ->withInput()
                    ->with('aba_ativa', 'login')
                    ->with('erro', "O nome informado não confere com o CPF cadastrado. (Cadastrado como: {$usuario->nome})");
            }

        } else {
            // Novo cliente: Nome e CPF obrigatórios, E-mail e Telefone opcionais
            $request->validate([
                'nome'     => 'required|string|max:100',
                'cpf'      => 'required|string|max:20',
                'email'    => 'nullable|email|max:150',
                'telefone' => 'nullable|string|max:20',
            ], [
                'nome.required' => 'Por favor, informe seu nome completo.',
                'cpf.required'  => 'Por favor, informe seu CPF.',
                'email.email'   => 'Por favor, informe um endereço de e-mail válido.',
            ]);

            $cpfLimpo = preg_replace('/[^0-9]/', '', $request->cpf);

            // Verifica se o CPF já está cadastrado
            $existente = Usuarios::where(function($q) use ($request, $cpfLimpo) {
                $q->where('cpf', $request->cpf);
                if (!empty($cpfLimpo)) {
                    $q->orWhere('cpf', $cpfLimpo);
                }
            })->first();

            if ($existente) {
                return redirect()->back()
                    ->withInput()
                    ->with('aba_ativa', 'login')
                    ->with('erro', 'Este CPF já está cadastrado! Você pode entrar diretamente pela aba "Já sou cliente".');
            }

            // Cadastra o novo usuário
            $usuario = Usuarios::create([
                'nome'     => trim($request->nome),
                'cpf'      => $request->cpf,
                'email'    => $request->filled('email') ? trim($request->email) : null,
                'telefone' => $request->filled('telefone') ? trim($request->telefone) : null,
            ]);
        }

        // Garante a existência do carrinho padrão ID 1
        $stroller = SmartStroller::firstOrCreate(
            ['id_stroller' => 1],
            [
                'numero_identificacao' => 'STROLLER-01',
                'nivel_bateria'        => 100,
                'status'               => 'Disponivel'
            ]
        );

        // Cria a nova compra
        $compra = Compras::create([
            'id_usuario'  => $usuario->id_usuario,
            'id_stroller' => $stroller->id_stroller,
            'valor_total' => 0.00,
            'status'      => 'Em Andamento',
            'data_inicio' => now(),
        ]);

        // Grava as informações na sessão do navegador
        session([
            'id_compra'     => $compra->id_compra,
            'nomeComprador' => $usuario->nome
        ]);

        return redirect('/layout');
    }

    /**
     * Exibe a tela principal do carrinho/layout
     */
    public function exibirLayout()
    {
        $id_compra = session('id_compra');

        if (!$id_compra) {
            return redirect('/')->with('erro', 'Por favor, informe seus dados para iniciar a compra.');
        }

        $compra = Compras::with('itens.produto')->findOrFail($id_compra);
        $nomeComprador = session('nomeComprador');
        $pedidos = Produtos::all();

        return view('layout', compact('compra', 'nomeComprador', 'pedidos'));
    }

    /**
     * Emite o recibo final e marca a compra como Concluida
     */
    public function emitirRecibo($id_compra)
    {
        $compra = Compras::with(['usuario', 'itens.produto', 'stroller'])->findOrFail($id_compra);

        if ($compra->status === 'Em Andamento') {
            $compra->update([
                'status'   => 'Concluida',
                'data_fim' => now()
            ]);
        }

        session()->forget('id_compra');
        session()->forget('nomeComprador');

        return view('imprimir_recibo', compact('compra'));
    }
}