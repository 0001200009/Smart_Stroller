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
        $idStroller = (int) $request->input('id_stroller', $request->query('stroller_id', 1));

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
            $usuario = Usuarios::where(function ($q) use ($request, $cpfLimpo) {
                $q->where('cpf', $request->cpf);
                if (!empty($cpfLimpo)) {
                    $q->orWhere('cpf', $cpfLimpo);
                }
            })->first();

            if (!$usuario) {
                return redirect()->back()
                    ->withInput()
                    ->with('aba_ativa', 'login')
                    ->with('erro', 'CPF não encontrado. Caso ainda não tenha cadastro, acesse a aba "Primeiro acesso".');
            }

            // Verifica se o nome confere de forma flexível (case-insensitive)
            $nomeCadastrado = trim($usuario->nome);
            if (
                mb_strtolower($nomeCadastrado, 'UTF-8') !== mb_strtolower($nomeDigitado, 'UTF-8') &&
                stripos($nomeCadastrado, $nomeDigitado) === false &&
                stripos($nomeDigitado, $nomeCadastrado) === false
            ) {
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
            $existente = Usuarios::where(function ($q) use ($request, $cpfLimpo) {
                $q->where('cpf', $request->cpf);
                if (!empty($cpfLimpo)) {
                    $q->orWhere('cpf', $cpfLimpo);
                }
            })->first();

            if ($existente) {
                return redirect()->back()
                    ->withInput()
                    ->with('aba_ativa', 'login')
                    ->with('erro', 'Este CPF já está cadastrado! Você pode entrar diretamente pela aba "Já sou cadastrado".');
            }

            // Cadastra o novo usuário
            $usuario = Usuarios::create([
                'nome'     => trim($request->nome),
                'cpf'      => $request->cpf,
                'email'    => $request->filled('email') ? trim($request->email) : null,
                'telefone' => $request->filled('telefone') ? trim($request->telefone) : null,
            ]);
        }

        // Garante a existência do carrinho correspondente
        $stroller = SmartStroller::firstOrCreate(
            ['id_stroller' => $idStroller],
            [
                'numero_identificacao' => 'STROLLER-' . str_pad($idStroller, 2, '0', STR_PAD_LEFT),
                'nivel_bateria'        => 100,
                'status'               => 'Em Uso',
                'ultima_sincronizacao' => now(),
            ]
        );

        $stroller->update([
            'status'               => 'Em Uso',
            'ultima_sincronizacao' => now(),
        ]);

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
            'nomeComprador' => $usuario->nome,
            'id_stroller'   => $stroller->id_stroller,
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
            return redirect('/sistema')->with('erro', 'Por favor, identifique-se para iniciar seu carrinho.');
        }

        $compra = Compras::with(['itens.produto', 'usuario', 'stroller'])->findOrFail($id_compra);
        $nomeComprador = session('nomeComprador', $compra->usuario->nome ?? 'Cliente');

        // Sugestões de promoção para incentivo de ticket médio (Pitch Item 4)
        $promocoes = Produtos::where('status', 'em promocao')->take(4)->get();
        $stroller = $compra->stroller ?? SmartStroller::find(1);

        $valorTotal = (float) $compra->valor_total;
        $impostosAprox = round($valorTotal * 0.185, 2);

        return view('layout', compact('compra', 'nomeComprador', 'promocoes', 'stroller', 'impostosAprox'));
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

            if ($compra->stroller) {
                $compra->stroller->update([
                    'status'               => 'Disponivel',
                    'ultima_sincronizacao' => now(),
                ]);
            }
        }

        $impostosAprox = round(((float) $compra->valor_total) * 0.185, 2);

        session()->forget('id_compra');
        session()->forget('nomeComprador');
        session()->forget('id_stroller');

        return view('imprimir_recibo', compact('compra', 'impostosAprox'));
    }
}
