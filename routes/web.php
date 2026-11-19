<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiteControllers;
use App\Http\Controllers\AdminControllers;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ArduinoController;


Route::get('/sistema', [SiteControllers::class, 'sistema']);
Route::post('/compra/iniciar', [CheckoutController::class, 'iniciarCompra']);
Route::get('/layout', [CheckoutController::class, 'exibirLayout']);
Route::get('/final', [SiteControllers::class, 'final']);
Route::get('/', [SiteControllers::class, 'bemVindo']);
Route::get('/admin', [SiteControllers::class, 'admin']);

Route::get('/login', [AdminControllers::class, 'login']);



Route::get('/admin/alertas', [SiteControllers::class, 'alertas']);


Route::get('/admin', [SiteControllers::class, 'dashboard']);

//Página de Clientes Admin
Route::post('/admin/clientes/salvar', [AdminControllers::class, 'adicionarCliente']);
Route::get('/admin/clientes/editar/{id_usuario}', [AdminControllers::class, 'carregarTelaEdicaoCliente']);
Route::put('/admin/clientes/salvar/{id_usuario}', [AdminControllers::class, 'editarCliente']);
Route::get('/admin/clientes/excluir/{id_usuario}', [AdminControllers::class, 'excluirCliente']);
Route::get('/admin/clientes', [AdminControllers::class, 'clientes']);

//Página de Produtos Admin
Route::post('/admin/produtos/salvar', [AdminControllers::class, 'salvarProduto']);
Route::get('/admin/produtos/editar/{id_produto}', [AdminControllers::class, 'carregarTelaEdicao']);
Route::put('/admin/produtos/salvar/{id_produto}', [AdminControllers::class, 'editarProduto']);
Route::get('/admin/produtos/excluir/{id_produto}', [AdminControllers::class, 'excluirProduto']);
Route::get('/admin/produtos', [AdminControllers::class, 'produtos']);

//Página de Compras Admin
Route::get('/admin/compras', [AdminControllers::class, 'compras']);
Route::post('/admin/compras/salvar', [AdminControllers::class, 'adicionarCompra']);
Route::get('/admin/compras/editar/{id_compra}', [AdminControllers::class, 'carregarTelaEdicaoCompra']);
Route::put('/admin/compras/salvar/{id_compra}', [AdminControllers::class, 'editarCompra']);
Route::get('/admin/compras/excluir/{id_compra}', [AdminControllers::class, 'excluirCompra']);

Route::get('/layout/excluir/{id_produto}', [SiteControllers::class, 'excluirProduto']);

Route::post('/admin/strollers/salvar', [AdminControllers::class, 'salvarStroller']);

Route::get('/recibo/imprimir/{id}', [CheckoutController::class, 'emitirRecibo']);

Route::get('/carrinho/{id_compra}', [ArduinoController::class, 'buscarCarrinho']);