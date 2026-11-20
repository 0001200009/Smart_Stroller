<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteControllers;
use App\Http\Controllers\AdminControllers;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ArduinoController;
use App\Http\Controllers\RelatoriosController; // Importação adicionada

// Rotas do Totem / Cliente
Route::get('/', [SiteControllers::class, 'bemVindo'])->name('home');
Route::get('/sistema', [SiteControllers::class, 'sistema'])->name('sistema');
Route::post('/compra/iniciar', [CheckoutController::class, 'iniciarCompra'])->name('compra.iniciar');
Route::get('/layout', [CheckoutController::class, 'exibirLayout'])->name('layout');
Route::get('/layout/excluir/{id_produto}', [SiteControllers::class, 'excluirProduto'])->name('layout.excluir');
Route::get('/final', [SiteControllers::class, 'final'])->name('final');
Route::get('/recibo/imprimir/{id}', [CheckoutController::class, 'emitirRecibo'])->name('recibo.imprimir');

// Rota de sincronização do carrinho (compatível com chamadas sem prefixo /api)
Route::get('/carrinho/{id_compra}', [ArduinoController::class, 'buscarCarrinho'])->name('carrinho.buscar');

// Autenticação Administrativa
Route::get('/login', [AdminControllers::class, 'login'])->name('login');
Route::post('/login', [AdminControllers::class, 'autenticar'])->name('login.post');
Route::get('/logout', [AdminControllers::class, 'logout'])->name('logout');

// Painel Administrativo
Route::get('/admin', [SiteControllers::class, 'dashboard'])->name('admin.dashboard');

// Relatórios
Route::get('/admin/relatorios', [RelatoriosController::class, 'index'])->name('admin.relatorios');
Route::get('/admin/relatorios/export', [RelatoriosController::class, 'export'])->name('admin.relatorios.export');

// Alertas e Carrinhos
Route::get('/admin/alertas', [SiteControllers::class, 'alertas'])->name('admin.alertas');
Route::get('/admin/carrinhos', [SiteControllers::class, 'carrinhos'])->name('admin.carrinhos');
Route::post('/admin/strollers/salvar', [AdminControllers::class, 'salvarStroller'])->name('admin.strollers.salvar');

// Gestão de Clientes Admin
Route::get('/admin/clientes', [AdminControllers::class, 'clientes'])->name('admin.clientes');
Route::post('/admin/clientes/salvar', [AdminControllers::class, 'adicionarCliente'])->name('admin.clientes.salvar');
Route::get('/admin/clientes/editar/{id_usuario}', [AdminControllers::class, 'carregarTelaEdicaoCliente'])->name('admin.clientes.editar');
Route::put('/admin/clientes/salvar/{id_usuario}', [AdminControllers::class, 'editarCliente'])->name('admin.clientes.atualizar');
Route::get('/admin/clientes/excluir/{id_usuario}', [AdminControllers::class, 'excluirCliente'])->name('admin.clientes.excluir');

// Gestão de Produtos Admin
Route::get('/admin/produtos', [AdminControllers::class, 'produtos'])->name('admin.produtos');
Route::post('/admin/produtos/salvar', [AdminControllers::class, 'salvarProduto'])->name('admin.produtos.salvar');
Route::get('/admin/produtos/editar/{id_produto}', [AdminControllers::class, 'carregarTelaEdicao'])->name('admin.produtos.editar');
Route::put('/admin/produtos/salvar/{id_produto}', [AdminControllers::class, 'editarProduto'])->name('admin.produtos.atualizar');
Route::get('/admin/produtos/excluir/{id_produto}', [AdminControllers::class, 'excluirProduto'])->name('admin.produtos.excluir');

// Gestão de Compras Admin
Route::get('/admin/compras', [AdminControllers::class, 'compras'])->name('admin.compras');
Route::post('/admin/compras/salvar', [AdminControllers::class, 'adicionarCompra'])->name('admin.compras.salvar');
Route::get('/admin/compras/editar/{id_compra}', [AdminControllers::class, 'carregarTelaEdicaoCompra'])->name('admin.compras.editar');
Route::put('/admin/compras/salvar/{id_compra}', [AdminControllers::class, 'editarCompra'])->name('admin.compras.atualizar');
Route::get('/admin/compras/excluir/{id_compra}', [AdminControllers::class, 'excluirCompra'])->name('admin.compras.excluir');