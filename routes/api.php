<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArduinoController;
use App\Http\Controllers\CarrinhoController;

// Rota recebida pelo Python/Arduino/ESP32 quando uma tag RFID é lida
Route::post('/rfid/read', [ArduinoController::class, 'registrarLeitura']);

// Rota consultada pelo JavaScript da tela do carrinho (layout.blade.php) para atualizar os itens em tempo real
Route::get('/carrinho/{id_compra}', [ArduinoController::class, 'buscarCarrinho']);

// Rota alternativa com dados brutos
Route::get('/carrinho/dados/{id_compra}', [CarrinhoController::class, 'obterDadosCarrinho']);
