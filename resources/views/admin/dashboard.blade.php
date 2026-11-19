@extends('admin.index')
@section('conteudo')
<section id="dashboard" class="section active">
    <div class="top"><h1>Visão Geral</h1></div>
    <div class="cards">
        <div class="card">
            <h3>Itens no Carrinho</h3>
            <p id="dashProdutos">{{ $itensNoCarrinho }}</p>
        </div>
        <div class="card">
            <h3>Valor Total</h3>
            <p id="dashTotal">R$ {{ number_format($valorTotal, 2, ',', '.') }}</p>
        </div>
        <div class="card">
            <h3>Nível de Bateria</h3>
            <p>{{ $nivelBateria }}%</p>
        </div>
    </div>
    <div class="box">
        <h2>Status do Dispositivo</h2>
        @if($compraAtiva)
            <p><strong>SmartStroller #{{ $compraAtiva->id_stroller }}</strong> está operando em compra ativa (Pedido #{{ $compraAtiva->id_compra }}).</p>
        @else
            <p>O SmartStroller está conectado e aguardando novos clientes.</p>
        @endif
    </div>
    <div class="box">
        <h2>Localização do Carrinho #01</h2>
        <div class="map">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 6px;">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
            </svg>
            Corredor 4 - Higiene Pessoal
        </div>
    </div>

    
</section>
@endsection
