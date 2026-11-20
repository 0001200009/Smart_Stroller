<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('smart_stroller')) {
            Schema::create('smart_stroller', function (Blueprint $table) {
                $table->increments('id_stroller');
                $table->string('numero_identificacao', 50)->unique();
                $table->integer('nivel_bateria')->default(100);
                $table->enum('status', ['Disponivel', 'Em Uso', 'Pagamento', 'Manutencao'])->default('Disponivel');
                $table->dateTime('ultima_sincronizacao')->nullable();
                $table->dateTime('data_criacao')->nullable();
                $table->dateTime('data_remocao')->nullable();
                $table->dateTime('data_atualizacao')->nullable();
            });
        }

        if (!Schema::hasTable('usuarios')) {
            Schema::create('usuarios', function (Blueprint $table) {
                $table->increments('id_usuario');
                $table->string('nome', 100);
                $table->string('email', 150)->nullable()->unique();
                $table->string('cpf', 100)->nullable()->unique();
                $table->string('telefone', 20)->nullable();
                $table->dateTime('data_criacao')->nullable();
                $table->dateTime('data_atualizacao')->nullable();
                $table->dateTime('data_remocao')->nullable();
            });
        }

        if (!Schema::hasTable('categorias')) {
            Schema::create('categorias', function (Blueprint $table) {
                $table->increments('id_categoria');
                $table->string('nome', 100)->unique();
                $table->dateTime('data_criacao')->nullable();
                $table->dateTime('data_remocao')->nullable();
                $table->dateTime('data_atualizacao')->nullable();
            });
        }

        if (!Schema::hasTable('produtos')) {
            Schema::create('produtos', function (Blueprint $table) {
                $table->increments('id_produto');
                $table->string('codigo_rfid', 100)->nullable()->unique();
                $table->string('codigo_barras', 50)->nullable()->unique();
                $table->string('nome', 150);
                $table->decimal('preco', 10, 2);
                $table->integer('estoque')->default(0);
                $table->unsignedInteger('id_categoria')->nullable();
                $table->enum('status', ['disponivel', 'em promocao', 'em falta'])->default('disponivel');
                $table->dateTime('data_criacao')->nullable();
                $table->dateTime('data_atualizacao')->nullable();
                $table->dateTime('data_remocao')->nullable();

                $table->foreign('id_categoria')->references('id_categoria')->on('categorias')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('compras')) {
            Schema::create('compras', function (Blueprint $table) {
                $table->increments('id_compra');
                $table->unsignedInteger('id_usuario');
                $table->unsignedInteger('id_stroller');
                $table->dateTime('data_inicio')->nullable();
                $table->dateTime('data_fim')->nullable();
                $table->decimal('valor_total', 10, 2)->default(0.00);
                $table->enum('status', ['Em Andamento', 'Pagamento', 'Concluida', 'Cancelada'])->default('Em Andamento');
                $table->dateTime('data_criacao')->nullable();
                $table->dateTime('data_atualizacao')->nullable();
                $table->dateTime('data_remocao')->nullable();

                $table->foreign('id_usuario')->references('id_usuario')->on('usuarios')->cascadeOnDelete();
                $table->foreign('id_stroller')->references('id_stroller')->on('smart_stroller')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('itens_compra')) {
            Schema::create('itens_compra', function (Blueprint $table) {
                $table->increments('id_item');
                $table->unsignedInteger('id_compra');
                $table->unsignedInteger('id_produto');
                $table->integer('quantidade')->default(1);
                $table->decimal('preco_unitario', 10, 2);
                $table->decimal('subtotal', 10, 2);
                $table->dateTime('data_criacao')->nullable();
                $table->dateTime('data_atualizacao')->nullable();
                $table->dateTime('data_remocao')->nullable();

                $table->foreign('id_compra')->references('id_compra')->on('compras')->cascadeOnDelete();
                $table->foreign('id_produto')->references('id_produto')->on('produtos')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('pagamentos')) {
            Schema::create('pagamentos', function (Blueprint $table) {
                $table->increments('id_pagamento');
                $table->unsignedInteger('id_compra')->unique();
                $table->enum('metodo', ['PIX', 'Credito', 'Debito', 'CarteiraDigital'])->nullable();
                $table->decimal('valor', 10, 2)->nullable();
                $table->enum('status', ['Pendente', 'Aprovado', 'Recusado'])->default('Pendente');
                $table->dateTime('data_pagamento')->nullable();
                $table->dateTime('data_criacao')->nullable();
                $table->dateTime('data_remocao')->nullable();
                $table->dateTime('data_atualizacao')->nullable();

                $table->foreign('id_compra')->references('id_compra')->on('compras')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('historico_uso')) {
            Schema::create('historico_uso', function (Blueprint $table) {
                $table->increments('id_uso');
                $table->unsignedInteger('id_stroller')->nullable();
                $table->string('descricao', 255)->nullable();
                $table->dateTime('data_uso')->nullable();
                $table->dateTime('data_criacao')->nullable();
                $table->dateTime('data_remocao')->nullable();
                $table->dateTime('data_atualizacao')->nullable();

                $table->foreign('id_stroller')->references('id_stroller')->on('smart_stroller')->nullOnDelete();
            });
        }

        if (!Schema::hasTable('leituras_rfid')) {
            Schema::create('leituras_rfid', function (Blueprint $table) {
                $table->increments('id_leitura');
                $table->unsignedInteger('id_stroller');
                $table->unsignedInteger('id_produto');
                $table->enum('acao', ['Entrada', 'Saida']);
                $table->dateTime('data_leitura')->nullable();
                $table->dateTime('data_criacao')->nullable();
                $table->dateTime('data_remocao')->nullable();
                $table->dateTime('data_atualizacao')->nullable();

                $table->foreign('id_stroller')->references('id_stroller')->on('smart_stroller')->cascadeOnDelete();
                $table->foreign('id_produto')->references('id_produto')->on('produtos')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leituras_rfid');
        Schema::dropIfExists('historico_uso');
        Schema::dropIfExists('pagamentos');
        Schema::dropIfExists('itens_compra');
        Schema::dropIfExists('compras');
        Schema::dropIfExists('produtos');
        Schema::dropIfExists('categorias');
        Schema::dropIfExists('usuarios');
        Schema::dropIfExists('smart_stroller');
    }
};
