<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categorias;
use App\Models\Produtos;
use App\Models\SmartStroller;
use App\Models\Usuarios;

use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Carrinho Padrão
        SmartStroller::firstOrCreate(
            ['id_stroller' => 1],
            [
                'numero_identificacao' => 'STROLLER-01',
                'nivel_bateria'        => 100,
                'status'               => 'Disponivel',
                'ultima_sincronizacao' => now(),
            ]
        );

        // 2. Categorias
        $cats = ['Laticínios', 'Bebidas', 'Padaria', 'Açougue', 'Hortifruti', 'Limpeza'];
        foreach ($cats as $nomeCat) {
            Categorias::firstOrCreate(['nome' => $nomeCat]);
        }

        // 3. Usuários Iniciais
        Usuarios::firstOrCreate(
            ['cpf' => '123.456.789-00'],
            [
                'nome'     => 'Alefe Henrique',
                'email'    => 'alefe@aggly.com.br',
                'telefone' => '(11) 98765-4321',
            ]
        );

        Usuarios::firstOrCreate(
            ['cpf' => '987.654.321-00'],
            [
                'nome'     => 'Yasmim de Souza',
                'email'    => 'yasmim@aggly.com.br',
                'telefone' => '(11) 91234-5678',
            ]
        );

        // 4. Catálogo de Produtos com RFID
        $produtos = [
            ['337057F5', '789000001', 'Leite Integral 1L', 5.99, 100, 1, 'em promocao'],
            ['63DE5CF5', '789000002', 'Queijo Mussarela', 39.90, 50, 1, 'disponivel'],
            ['33E050F5', '789000003', 'Iogurte Natural', 3.50, 80, 1, 'disponivel'],
            ['3399F1F6', '789000004', 'Coca-Cola 2L', 9.99, 120, 2, 'em promocao'],
            ['RF005', '789000005', 'Suco de Laranja', 7.50, 60, 2, 'disponivel'],
            ['RF006', '789000006', 'Água Mineral 1,5L', 3.00, 200, 2, 'disponivel'],
            ['RF007', '789000007', 'Pão Francês', 0.80, 300, 3, 'disponivel'],
            ['RF008', '789000008', 'Bolo de Chocolate', 15.00, 20, 3, 'em promocao'],
            ['RF009', '789000009', 'Carne Bovina 1kg', 35.00, 40, 4, 'disponivel'],
            ['RF010', '789000010', 'Frango 1kg', 18.00, 60, 4, 'disponivel'],
            ['RF011', '789000011', 'Banana 1kg', 6.00, 200, 5, 'em promocao'],
            ['RF012', '789000012', 'Maçã 1kg', 8.00, 150, 5, 'disponivel'],
            ['RF013', '789000013', 'Alface Americana', 4.50, 90, 5, 'disponivel'],
            ['RF014', '789000014', 'Tomate 1kg', 7.20, 110, 5, 'disponivel'],
            ['RF015', '789000015', 'Detergente Ypê', 2.99, 120, 6, 'disponivel'],
            ['RF016', '789000016', 'Sabão em Pó', 18.50, 50, 6, 'disponivel'],
            ['RF017', '789000017', 'Amaciante 2L', 16.90, 40, 6, 'em falta'],
            ['RF018', '789000018', 'Água Sanitária', 5.50, 70, 6, 'em promocao'],
            ['RF019', '789000019', 'Desinfetante', 7.90, 80, 6, 'disponivel'],
            ['RF020', '789000020', 'Esponja de Limpeza', 2.50, 150, 6, 'em falta'],
        ];

        foreach ($produtos as $p) {
            Produtos::firstOrCreate(
                ['codigo_rfid' => $p[0]],
                [
                    'codigo_barras' => $p[1],
                    'nome'          => $p[2],
                    'preco'         => $p[3],
                    'estoque'       => $p[4],
                    'id_categoria'  => $p[5],
                    'status'        => $p[6],
                ]
            );
        }
    }
}
