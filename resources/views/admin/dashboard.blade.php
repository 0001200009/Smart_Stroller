@extends('admin.index')

@section('conteudo')
<section id="dashboard" class="section active">
    <div class="top" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700;">Visão Geral do Sistema</h1>
            <p style="color: #94a3b8; font-size: 14px; margin-top: 4px;">Monitoramento de frotas IoT, checkouts rápidos e prevenção de perdas em tempo real</p>
        </div>
        <div style="display: flex; gap: 15px; align-items: center;">
            
            <!-- SELETOR DE CARRINHOS -->
            <form action="{{ url()->current() }}" method="GET" style="margin: 0;">
                <div style="display: flex; align-items: center; gap: 8px; background: #1e293b; padding: 8px 12px; border-radius: 8px; border: 1px solid #334155;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    <select name="stroller_id" onchange="this.form.submit()" style="background: transparent; border: none; color: #ffffff; font-size: 14px; font-weight: 600; outline: none; cursor: pointer;">
                        @foreach($carrinhos as $c)
                            <option value="{{ $c->id_stroller }}" style="background: #1e293b; color: #fff;" {{ request('stroller_id', $carrinhoAtual->id_stroller ?? 1) == $c->id_stroller ? 'selected' : '' }}>
                                {{ $c->numero_identificacao }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>

            <span style="display: inline-flex; align-items: center; gap: 6px; background: rgba(16, 185, 129, 0.15); color: #34d399; font-size: 13px; font-weight: 600; padding: 6px 12px; border-radius: 20px;">
                <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; display: inline-block; box-shadow: 0 0 8px #10b981;"></span>
                Rede IoT Conectada
            </span>
        </div>
    </div>

    <!-- Cards Principais de Indicadores -->
    <div class="cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <h3 style="font-size: 12px; color: #94a3b8; text-transform: uppercase;">Itens no Carrinho Ativo</h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="9" cy="21" r="1"></circle>
                    <circle cx="20" cy="21" r="1"></circle>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                </svg>
            </div>
            <p id="dashProdutos" style="font-size: 28px; font-weight: 800; color: #ffffff;">{{ $itensNoCarrinho }} <span style="font-size: 14px; font-weight: normal; color: #94a3b8;">unidades</span></p>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <h3 style="font-size: 12px; color: #94a3b8; text-transform: uppercase;">Valor do Carrinho Ativo</h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"></line>
                    <path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                </svg>
            </div>
            <p id="dashTotal" style="font-size: 28px; font-weight: 800; color: #10b981;">R$ {{ number_format($valorTotal, 2, ',', '.') }}</p>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <h3 style="font-size: 12px; color: #94a3b8; text-transform: uppercase;">Bateria Smart Stroller</h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="{{ $nivelBateria > 25 ? '#10b981' : '#ef4444' }}" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="7" width="16" height="10" rx="2" ry="2"></rect>
                    <line x1="22" y1="11" x2="22" y2="13"></line>
                </svg>
            </div>
            <p style="font-size: 28px; font-weight: 800; color: {{ $nivelBateria > 25 ? '#ffffff' : '#ef4444' }};">{{ $nivelBateria }}% <span style="font-size: 13px; font-weight: normal; color: #94a3b8;">(Até 12h)</span></p>
        </div>

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                <h3 style="font-size: 12px; color: #94a3b8; text-transform: uppercase;">Tempo Médio Checkout</h3>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
            </div>
            <p style="font-size: 28px; font-weight: 800; color: #f59e0b;">25 seg <span style="font-size: 13px; font-weight: normal; color: #94a3b8;">(vs 7 min)</span></p>
        </div>
    </div>

    <!-- Seção de Segurança Antifraude & Status do Dispositivo (Pitch Point 3 & 4) -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
        <!-- Status do Dispositivo -->
        <div class="box">
            <h2 style="display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect>
                    <line x1="8" y1="21" x2="16" y2="21"></line>
                    <line x1="12" y1="17" x2="12" y2="21"></line>
                </svg>
                Dispositivo {{ $carrinhoAtual->numero_identificacao ?? 'Smart Stroller' }}
            </h2>

            @if($compraAtiva)
                <div style="padding: 14px; background: rgba(6, 182, 212, 0.1); border-radius: 10px; border-left: 4px solid #06b6d4; margin-bottom: 15px;">
                    <p style="margin: 0; font-size: 15px;"><strong>Compra em Andamento</strong> — Pedido #{{ $compraAtiva->id_compra }}</p>
                    <p style="margin: 4px 0 0 0; color: #94a3b8; font-size: 13px;">Cliente: <strong>{{ $compraAtiva->usuario->nome ?? 'Cliente Anônimo' }}</strong></p>
                    <p style="margin: 2px 0 0 0; color: #94a3b8; font-size: 13px;">Início: {{ \Carbon\Carbon::parse($compraAtiva->data_inicio)->format('d/m/Y H:i:s') }}</p>
                </div>
            @else
                <div style="padding: 14px; background: rgba(16, 185, 129, 0.1); border-radius: 10px; border-left: 4px solid #10b981; margin-bottom: 15px;">
                    <p style="margin: 0; font-size: 14px; color: #34d399;"><strong>Status: Disponível</strong></p>
                    <p style="margin: 4px 0 0 0; color: #94a3b8; font-size: 13px;">O carrinho está posicionado na entrada, aguardando próximo cliente.</p>
                </div>
            @endif

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 13px; color: #94a3b8;">
                <div style="background: #0f172a; padding: 10px; border-radius: 8px;">
                    <span style="display: block; color: #64748b; font-size: 11px;">LEITOR RFID UHF</span>
                    <strong style="color: #ffffff;">3 Antenas Ativas</strong>
                </div>
                <div style="background: #0f172a; padding: 10px; border-radius: 8px;">
                    <span style="display: block; color: #64748b; font-size: 11px;">PROCESSADOR EMBARCADO</span>
                    <strong style="color: #ffffff;">Raspberry Pi + Wi-Fi</strong>
                </div>
            </div>
        </div>

        <!-- Módulo de Segurança Antifraude & Câmera HD -->
        <div class="box">
            <h2 style="display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
                Segurança Antifraude & Visão Computacional
            </h2>
            <div style="padding: 14px; background: rgba(16, 185, 129, 0.1); border-radius: 10px; border-left: 4px solid #10b981; margin-bottom: 15px;">
                <p style="margin: 0; font-size: 14px; color: #34d399;"><strong>Câmera HD Interna: Validação Cruzada Normal</strong></p>
                <p style="margin: 4px 0 0 0; color: #94a3b8; font-size: 13px;">Volume óptico do cesto compatível com as tags RFID registradas (0 quebras ou furtos detectados).</p>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 13px; color: #94a3b8;">
                <div style="background: #0f172a; padding: 10px; border-radius: 8px;">
                    <span style="display: block; color: #64748b; font-size: 11px;">GEOFENCING / PERÍMETRO GPS</span>
                    <strong style="color: #34d399;">Dentro do Perímetro</strong>
                </div>
                <div style="background: #0f172a; padding: 10px; border-radius: 8px;">
                    <span style="display: block; color: #64748b; font-size: 11px;">PREVENÇÃO DE PERDAS</span>
                    <strong style="color: #34d399;">100% Protegido</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Localização GPS da Frota -->
    <div class="box">
        <h2 style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                <circle cx="12" cy="10" r="3"></circle>
            </svg>
            Rastreamento GPS & Mapa da Loja - {{ $carrinhoAtual->numero_identificacao ?? 'Smart Stroller' }}
        </h2>
        <div class="map" style="background: #1e293b; border: 1px dashed #334155; border-radius: 12px; height: 160px; display: flex; flex-direction: column; justify-content: center; align-items: center; gap: 8px;">
            <div style="display: flex; align-items: center; gap: 8px; font-size: 16px; color: #f87171; font-weight: 700;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon>
                </svg>
                Setor: Corredor 4 - Higiene Pessoal & Limpeza
            </div>
            <p style="color: #94a3b8; font-size: 13px; margin: 0;">Coordenadas internas: Bloco B2 | Distância do Caixa Express: 18 metros</p>
        </div>
    </div>
</section>
@endsection