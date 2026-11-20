@extends('admin.index')

@section('conteudo')
<section id="alertas" class="section active">
    <div class="top" style="margin-bottom: 25px;">
        <h1 style="font-size: 26px; font-weight: 700;">Central de Notificações & Prevenção de Quebras</h1>
        <p style="color: #94a3b8; font-size: 14px; margin-top: 4px;">Auditoria de segurança antifraude, telemetria de frota e gestão de reposição de gôndola</p>
    </div>

    <!-- Alertas Críticos de Ruptura de Estoque (Em Falta) -->
    @if($produtosEmFalta->count() > 0)
        <div class="alertBox" style="margin-bottom: 20px; background: rgba(239, 68, 68, 0.15); border-left: 4px solid #ef4444; padding: 18px 20px; border-radius: 12px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                    <line x1="12" y1="9" x2="12" y2="13"></line>
                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                </svg>
                <span style="font-size: 16px; font-weight: 700; color: #fca5a5;">
                    ALERTA DE REPOSIÇÃO: {{ $produtosEmFalta->count() }} item(ns) em falta nas gôndolas!
                </span>
            </div>
            <p style="color: #cbd5e1; font-size: 13px; margin: 0 0 12px 0;">
                Os seguintes itens estão marcados como esgotados no sistema e os clientes não conseguirão adicioná-los no carrinho inteligente:
            </p>
            <ul style="margin: 0 0 0 20px; color: #f87171; font-size: 14px;">
                @foreach($produtosEmFalta as $falta)
                    <li style="margin-bottom: 4px;">
                        <strong>{{ $falta->nome }}</strong> (RFID: {{ $falta->codigo_rfid }}) — Setor: {{ $falta->categoria->nome ?? 'Geral' }}
                        <a href="/admin/produtos/editar/{{ $falta->id_produto }}" style="color: #38bdf8; margin-left: 10px; text-decoration: underline; font-size: 12px;">Reabastecer</a>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Alertas Preventivos de Estoque Baixo -->
    @if($produtosEstoqueBaixo->count() > 0)
        <div style="margin-bottom: 20px; background: rgba(245, 158, 11, 0.12); border-left: 4px solid #f59e0b; padding: 16px 20px; border-radius: 12px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span style="font-size: 15px; font-weight: 700; color: #fcd34d;">
                    Atenção: {{ $produtosEstoqueBaixo->count() }} produto(s) com estoque baixo (&lt; 20 un)
                </span>
            </div>
            <div style="display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px;">
                @foreach($produtosEstoqueBaixo as $baixo)
                    <span style="background: rgba(0, 0, 0, 0.3); padding: 4px 10px; border-radius: 6px; font-size: 12px; color: #e2e8f0;">
                        {{ $baixo->nome }}: <strong>{{ $baixo->estoque }} un</strong>
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Painel de Auditoria Antifraude (Pitch Point 2, 3 & 4) -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 25px;">
        <div class="box">
            <h2 style="display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                </svg>
                Prevenção de Furtos (Câmera HD + RFID)
            </h2>
            <div style="background: #0f172a; padding: 14px; border-radius: 10px; margin-bottom: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <span style="font-size: 13px; color: #94a3b8;">Validação Óptica do Cesto</span>
                    <strong style="color: #34d399; font-size: 13px;">100% Sincronizado</strong>
                </div>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Nenhuma divergência entre volume detectado pela câmera e tags lidas.</p>
            </div>
            <div style="background: #0f172a; padding: 14px; border-radius: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <span style="font-size: 13px; color: #94a3b8;">Troca Indevida de Etiquetas</span>
                    <strong style="color: #34d399; font-size: 13px;">0 Ocorrências</strong>
                </div>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Tags dinâmicas UHF protegidas contra regravação.</p>
            </div>
        </div>

        <div class="box">
            <h2 style="display: flex; align-items: center; gap: 8px;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon>
                </svg>
                Perímetro GPS da Loja (Geofencing)
            </h2>
            <div style="background: #0f172a; padding: 14px; border-radius: 10px; margin-bottom: 12px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <span style="font-size: 13px; color: #94a3b8;">Perímetro Autorizado</span>
                    <strong style="color: #34d399; font-size: 13px;">Frota no Interior da Loja</strong>
                </div>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Rastreador GPS ativo em todos os carrinhos contra extravio.</p>
            </div>
            <div style="background: #0f172a; padding: 14px; border-radius: 10px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <span style="font-size: 13px; color: #94a3b8;">Quebra Operacional Estimada</span>
                    <strong style="color: #34d399; font-size: 13px;">Reduzida de 1,3% para 0,1%</strong>
                </div>
                <p style="margin: 0; font-size: 12px; color: #64748b;">Economia direta em prevenção de perdas e quebras.</p>
            </div>
        </div>
    </div>

    <!-- Status Geral do Sistema se não houver problemas críticos -->
    @if($produtosEmFalta->count() === 0)
        <div class="box" style="display: flex; align-items: center; gap: 12px;">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <div>
                <p style="margin: 0; font-weight: 600; color: #ffffff;">Operação 100% Estável</p>
                <p style="margin: 2px 0 0 0; font-size: 13px; color: #94a3b8;">Todos os produtos com estoque disponível e nenhum incidente crítico reportado pela frota.</p>
            </div>
        </div>
    @endif
</section>
@endsection
