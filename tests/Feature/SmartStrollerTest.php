<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Usuarios;
use App\Models\Produtos;
use App\Models\SmartStroller;
use App\Models\Compras;
use App\Models\ItensCompra;

class SmartStrollerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }
    /**
     * Testa se as páginas públicas e administrativas retornam 200 OK
     */
    public function test_paginas_principais_carregam_com_sucesso(): void
    {
        $this->get('/')->assertStatus(200);
        $this->get('/sistema')->assertStatus(200);
        $this->get('/login')->assertStatus(200);
        $this->get('/admin')->assertStatus(200);
        $this->get('/admin/produtos')->assertStatus(200);
        $this->get('/admin/alertas')->assertStatus(200);
        $this->get('/admin/clientes')->assertStatus(200);
        $this->get('/admin/compras')->assertStatus(200);
    }

    /**
     * Testa o fluxo de autenticação de cliente existente no totem do carrinho
     */
    public function test_cliente_cadastrado_inicia_compra(): void
    {
        $usuario = Usuarios::firstOrCreate(
            ['cpf' => '111.222.333-44'],
            ['nome' => 'Cliente Teste Totem', 'email' => 'teste@stroller.com', 'telefone' => '(11) 99999-0000']
        );

        $response = $this->post('/compra/iniciar', [
            'tipo_acesso' => 'login',
            'nome'        => 'Cliente Teste Totem',
            'cpf'         => '111.222.333-44',
            'id_stroller' => 1,
        ]);

        $response->assertRedirect('/layout');
        $this->assertTrue(session()->has('id_compra'));
        $this->assertEquals('Cliente Teste Totem', session('nomeComprador'));
    }

    /**
     * Testa o fluxo de novo cadastro de cliente no totem
     */
    public function test_novo_cliente_cadastra_e_inicia_compra(): void
    {
        $cpfUnico = '999.' . rand(100, 999) . '.' . rand(100, 999) . '-88';

        $response = $this->post('/compra/iniciar', [
            'tipo_acesso' => 'cadastro',
            'nome'        => 'Novo Comprador Express',
            'cpf'         => $cpfUnico,
            'email'       => 'novo@express.com',
            'telefone'    => '(11) 98888-7777',
            'id_stroller' => 1,
        ]);

        $response->assertRedirect('/layout');
        $this->assertDatabaseHas('usuarios', ['cpf' => $cpfUnico]);
    }

    /**
     * Testa o endpoint IoT /api/rfid/read para adição (Entrada) e remoção (Saida) de produtos
     */
    public function test_leitura_rfid_esp32_adiciona_e_remove_produto(): void
    {
        $usuario = Usuarios::first();
        $stroller = SmartStroller::firstOrCreate(
            ['id_stroller' => 1],
            ['numero_identificacao' => 'STROLLER-01', 'nivel_bateria' => 100, 'status' => 'Em Uso']
        );

        $compra = Compras::create([
            'id_usuario'  => $usuario->id_usuario,
            'id_stroller' => $stroller->id_stroller,
            'valor_total' => 0.00,
            'status'      => 'Em Andamento',
            'data_inicio' => now(),
        ]);

        $produto = Produtos::whereNotNull('codigo_rfid')->first();
        $this->assertNotNull($produto, 'Deve haver ao menos um produto cadastrado com RFID');

        // 1. Simula leitura de Entrada (ESP32)
        $respEntrada = $this->postJson('/api/rfid/read', [
            'codigo_rfid' => $produto->codigo_rfid,
            'id_stroller' => 1,
            'acao'        => 'Entrada'
        ]);

        $respEntrada->assertStatus(200)
            ->assertJson([
                'status'  => 'sucesso',
                'produto' => $produto->nome,
            ]);

        $this->assertDatabaseHas('itens_compra', [
            'id_compra'  => $compra->id_compra,
            'id_produto' => $produto->id_produto,
            'quantidade' => 1
        ]);

        // 2. Consulta polling em tempo real
        $respPolling = $this->getJson('/api/carrinho/' . $compra->id_compra);
        $respPolling->assertStatus(200)
            ->assertJsonStructure([
                'itens',
                'quantidade_total',
                'impostos_total',
                'subtotal_liquido',
                'valor_total'
            ]);

        // 3. Simula leitura de Saida (ESP32)
        $respSaida = $this->postJson('/api/rfid/read', [
            'codigo_rfid' => $produto->codigo_rfid,
            'id_stroller' => 1,
            'acao'        => 'Saida'
        ]);

        $respSaida->assertStatus(200);
        $this->assertDatabaseMissing('itens_compra', [
            'id_compra'  => $compra->id_compra,
            'id_produto' => $produto->id_produto,
        ]);
    }

    /**
     * Testa emissão de cupom final e checkout rápido
     */
    public function test_emissao_recibo_conclui_compra(): void
    {
        $usuario = Usuarios::first();
        $compra = Compras::create([
            'id_usuario'  => $usuario->id_usuario,
            'id_stroller' => 1,
            'valor_total' => 25.50,
            'status'      => 'Em Andamento',
            'data_inicio' => now(),
        ]);

        $response = $this->get('/recibo/imprimir/' . $compra->id_compra);
        $response->assertStatus(200);
        $response->assertSee('SMART STROLLER AGGLY');

        $this->assertEquals('Concluida', $compra->fresh()->status);
    }
}
