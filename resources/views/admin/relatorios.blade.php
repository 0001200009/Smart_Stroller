@extends('admin.index')

@section('conteudo')
<section id="relatorios" class="section active">
    <!-- Header e Filtros -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 15px;">
        <div>
            <h1 style="font-size: 26px; font-weight: 700;">Relatórios e Análises</h1>
            <p style="color: #94a3b8; font-size: 14px; margin-top: 4px;">Acompanhe o faturamento e desempenho da sua loja</p>
        </div>

        <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            <a href="{{ route('admin.relatorios', ['periodo' => 'dia']) }}" class="btn-filter {{ $periodo == 'dia' ? 'active' : '' }}">Hoje</a>
            <a href="{{ route('admin.relatorios', ['periodo' => 'semana']) }}" class="btn-filter {{ $periodo == 'semana' ? 'active' : '' }}">Últimos 7 dias</a>
            <a href="{{ route('admin.relatorios', ['periodo' => 'mes']) }}" class="btn-filter {{ $periodo == 'mes' ? 'active' : '' }}">Este Mês</a>
            <a href="{{ route('admin.relatorios', ['periodo' => 'ano']) }}" class="btn-filter {{ $periodo == 'ano' ? 'active' : '' }}">Este Ano</a>
            <a href="{{ route('admin.relatorios.export', ['periodo' => $periodo, 'format' => 'json']) }}" class="btn-export" style="margin-left: 10px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: inline-block; vertical-align: middle; margin-right: 4px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                Exportar (JSON)
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 25px;">
        <div class="card" style="background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.2); padding: 20px; border-radius: 12px;">
            <h3 style="font-size: 12px; color: #34d399; text-transform: uppercase;">Faturamento Total</h3>
            <p style="font-size: 32px; font-weight: 800; color: #10b981; margin-top: 8px;">R$ {{ number_format($faturamentoTotal, 2, ',', '.') }}</p>
        </div>
        <div class="card" style="background: rgba(6, 182, 212, 0.05); border: 1px solid rgba(6, 182, 212, 0.2); padding: 20px; border-radius: 12px;">
            <h3 style="font-size: 12px; color: #67e8f9; text-transform: uppercase;">Ticket Médio</h3>
            <p style="font-size: 32px; font-weight: 800; color: #06b6d4; margin-top: 8px;">R$ {{ number_format($ticketMedio, 2, ',', '.') }}</p>
        </div>
        <div class="card" style="background: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.2); padding: 20px; border-radius: 12px;">
            <h3 style="font-size: 12px; color: #fbbf24; text-transform: uppercase;">Total de Vendas</h3>
            <p style="font-size: 32px; font-weight: 800; color: #f59e0b; margin-top: 8px;">{{ $totalCompras }}</p>
        </div>
    </div>

    <!-- Gráficos -->
    <!-- Gráficos -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-bottom: 25px;">
        
        <!-- Box do Gráfico de Faturamento -->
        <div class="box" style="padding: 20px;">
            <h3 style="font-size: 16px; margin-bottom: 15px; color: #fff;">Curva de Faturamento</h3>
            <!-- Wrapper limitador de altura -->
            <div style="position: relative; height: 250px; width: 100%;">
                <canvas id="chartFaturamento"></canvas>
            </div>
        </div>

        <!-- Box do Gráfico de Produtos -->
        <div class="box" style="padding: 20px;">
            <h3 style="font-size: 16px; margin-bottom: 15px; color: #fff;">Top 5 Produtos Vendidos</h3>
            <!-- Wrapper limitador de altura -->
            <div style="position: relative; height: 250px; width: 100%; display: flex; justify-content: center;">
                <canvas id="chartDistribuicao"></canvas>
            </div>
        </div>

    </div>

    <!-- Tabela de Últimas Transações -->
    <div class="box">
        <h3 style="font-size: 18px; margin-bottom: 15px; color: #fff;">Últimas Transações do Período</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                <thead>
                    <tr style="border-bottom: 1px solid #334155; color: #94a3b8;">
                        <th style="padding: 12px 15px; font-weight: 600;">ID</th>
                        <th style="padding: 12px 15px; font-weight: 600;">Cliente</th>
                        <th style="padding: 12px 15px; font-weight: 600;">Carrinho</th>
                        <th style="padding: 12px 15px; font-weight: 600;">Data/Hora</th>
                        <th style="padding: 12px 15px; font-weight: 600;">Valor</th>
                        <th style="padding: 12px 15px; font-weight: 600;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ultimasCompras as $c)
                    <tr style="border-bottom: 1px solid #1e293b; color: #e2e8f0;">
                        <td style="padding: 12px 15px;">#{{ $c->id_compra }}</td>
                        <td style="padding: 12px 15px;">{{ $c->usuario->nome ?? 'Cliente Anônimo' }}</td>
                        <td style="padding: 12px 15px;">{{ $c->stroller->numero_identificacao ?? '‑' }}</td>
                        <td style="padding: 12px 15px;">{{ \Carbon\Carbon::parse($c->data_inicio)->format('d/m/Y H:i') }}</td>
                        <td style="padding: 12px 15px; font-weight: 600;">R$ {{ number_format($c->valor_total, 2, ',', '.') }}</td>
                        <td style="padding: 12px 15px;">
                            <span style="background: {{ $c->status == 'Concluida' ? 'rgba(16, 185, 129, 0.15)' : 'rgba(245, 158, 11, 0.15)' }}; color: {{ $c->status == 'Concluida' ? '#34d399' : '#fbbf24' }}; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: bold;">
                                {{ $c->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding: 20px; text-align: center; color: #94a3b8;">Nenhuma compra encontrada neste período.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Scripts e Estilos Locais -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico 1: Curva de Faturamento
    const ctxF = document.getElementById('chartFaturamento').getContext('2d');
    new Chart(ctxF, {
        type: 'line',
        data: {
            labels: @json($faturamentoTempo->pluck('periodo')),
            datasets: [{
                label: 'Faturamento (R$)',
                data: @json($faturamentoTempo->pluck('total')),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.15)',
                borderWidth: 2,
                tension: 0.3,
                fill: true,
                pointBackgroundColor: '#10b981'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { color: '#1e293b' }, ticks: { color: '#94a3b8' } },
                x: { grid: { color: '#1e293b' }, ticks: { color: '#94a3b8' } }
            },
            plugins: { legend: { display: false } }
        }
    });

    // Gráfico 2: Top Produtos (Doughnut)
    // Trava de segurança inserida para evitar erros se o produto for deletado
    const topLabels = @json($topProdutos->map(fn($p) => $p->produto->nome ?? 'Produto Removido'));
    const topValues = @json($topProdutos->pluck('total_vendido'));
    const ctxD = document.getElementById('chartDistribuicao').getContext('2d');
    new Chart(ctxD, {
        type: 'doughnut',
        data: {
            labels: topLabels,
            datasets: [{
                data: topValues,
                backgroundColor: ['#06b6d4','#10b981','#f59e0b','#ef4444','#8b5cf6'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { position: 'right', labels: { color: '#e2e8f0', font: { size: 12 } } } 
            }
        }
    });
</script>

<style>
    .btn-filter {
        padding: 8px 16px;
        border-radius: 8px;
        background: #1e293b;
        border: 1px solid #334155;
        color: #94a3b8;
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
        transition: all 0.2s;
    }
    .btn-filter.active { background: #06b6d4; border-color: #06b6d4; color: #ffffff; }
    .btn-filter:hover:not(.active) { background: #334155; color: #ffffff; }
    
    .btn-export { 
        padding: 8px 16px; 
        border-radius: 8px; 
        background: #f59e0b; 
        color: #fff; 
        font-weight: 600; 
        font-size: 13px; 
        text-decoration: none; 
        transition: background 0.2s; 
    }
    .btn-export:hover { background: #d97706; }
</style>
@endsection