<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Stroller - Terminal Interativo</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f0f7fc, #2081c3);
            color: #1e293b;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .screen-container {
            width: 100%;
            max-width: 1100px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(14px);
            border-radius: 24px;
            padding: 25px 30px;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.8);
        }

        /* Barra de Status IoT Superior (Pitch: Bateria 12h, Raspberry Pi Wi-Fi, Câmera HD) */
        .iot-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #0f172a;
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .iot-status-group {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .iot-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 6px #10b981;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 18px;
            margin-bottom: 22px;
        }

        .header-title h1 {
            font-size: 1.9rem;
            color: #0f172a;
            font-weight: 800;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-title p {
            color: #64748b;
            font-size: 0.95rem;
            margin-top: 2px;
        }

        .comprador-badge {
            background: rgba(32, 129, 195, 0.1);
            color: #2081c3;
            padding: 8px 16px;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        /* Grid Principal de Conteúdo */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 25px;
        }

        @media (max-width: 900px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Tabela de Produtos no Carrinho */
        .cart-box {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
        }

        .tabela-produtos {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        .tabela-produtos th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            padding: 14px 16px;
            text-align: left;
            border-bottom: 2px solid #e2e8f0;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .tabela-produtos td {
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
            vertical-align: middle;
        }

        .tabela-produtos tr:last-child td {
            border-bottom: none;
        }

        .tag-rfid-mini {
            font-family: monospace;
            font-size: 0.75rem;
            color: #0284c7;
            background: #e0f2fe;
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            margin-top: 3px;
        }

        /* Coluna Lateral de Resumo e Promoções */
        .sidebar-right {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Card de Resumo de Valores */
        .resumo-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 22px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
        }

        .resumo-card h2 {
            font-size: 1.15rem;
            color: #0f172a;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .resumo-linha {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            font-size: 0.95rem;
            color: #64748b;
        }

        .resumo-linha.impostos {
            color: #94a3b8;
            font-size: 0.85rem;
            border-bottom: 1px dashed #e2e8f0;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }

        .resumo-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
            margin-bottom: 20px;
            padding-top: 8px;
        }

        .total-label {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
        }

        .total-valor {
            font-size: 1.8rem;
            font-weight: 800;
            color: #2081c3;
        }

        .btn-checkout {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            background: #2081c3;
            color: #ffffff;
            padding: 16px;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s ease;
            box-shadow: 0 6px 18px rgba(32, 129, 195, 0.35);
        }

        .btn-checkout:hover {
            background: #196ca7;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(32, 129, 195, 0.45);
        }

        /* Card de Promoções para Ticket Médio (Pitch Item 4) */
        .promo-card {
            background: linear-gradient(135deg, #f0fdf4, #e0f2fe);
            border: 1px solid #bae6fd;
            border-radius: 16px;
            padding: 20px;
        }

        .promo-card h3 {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0369a1;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .promo-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid rgba(186, 230, 253, 0.6);
            font-size: 0.88rem;
        }

        .promo-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .promo-nome {
            font-weight: 600;
            color: #0f172a;
        }

        .promo-preco {
            font-weight: 700;
            color: #059669;
        }

        .vazio-msg {
            padding: 45px 20px;
            text-align: center;
            color: #64748b;
        }

        .vazio-msg svg {
            margin-bottom: 12px;
            opacity: 0.6;
        }
    </style>
</head>
<body>

    <div class="screen-container">

        <!-- Barra de Status IoT (Pitch: Bateria 12h, RFID Ativo, Câmera HD) -->
        <div class="iot-topbar">
            <div class="iot-status-group">
                <span class="iot-badge">
                    <span class="status-dot"></span>
                    {{ $stroller->numero_identificacao ?? 'STROLLER-01' }}
                </span>
                <span class="iot-badge" style="color: #94a3b8;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M5 12.55a11 11 0 0 1 14.08 0"></path>
                        <path d="M1.42 9a16 16 0 0 1 21.16 0"></path>
                        <path d="M8.53 16.11a6 6 0 0 1 6.95 0"></path>
                        <line x1="12" y1="20" x2="12.01" y2="20"></line>
                    </svg>
                    Wi-Fi 5GHz
                </span>
                <span class="iot-badge" style="color: #94a3b8;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
                        <circle cx="12" cy="13" r="4"></circle>
                    </svg>
                    Câmera Antifraude Ativa
                </span>
            </div>

            <div class="iot-status-group">
                <span class="iot-badge" style="color: #34d399;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="7" width="16" height="10" rx="2" ry="2"></rect>
                        <line x1="22" y1="11" x2="22" y2="13"></line>
                    </svg>
                    Bateria {{ $stroller->nivel_bateria ?? 100 }}% (Até 12h)
                </span>
            </div>
        </div>

        <header>
            <div class="header-title">
                <h1>
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#2081c3" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                    Smart Stroller
                </h1>
                <p>Terminal Autônomo de Compras • Pedido #{{ str_pad($compra->id_compra, 4, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div class="comprador-badge">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                Olá, <strong>{{ $nomeComprador }}</strong>!
            </div>
        </header>

        <!-- Grade de Conteúdo Principal -->
        <div class="content-grid">

            <!-- Coluna Esquerda: Itens do Carrinho -->
            <div class="cart-box">
                <table class="tabela-produtos">
                    <thead>
                        <tr>
                            <th>Produto / RFID</th>
                            <th style="text-align: center;">Qtd</th>
                            <th style="text-align: right;">Preço Un.</th>
                            <th style="text-align: right;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="lista-itens">
                        @forelse($compra->itens as $item)
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: #0f172a;">{{ $item->produto->nome ?? 'Produto' }}</div>
                                    @if(!empty($item->produto->codigo_rfid))
                                        <span class="tag-rfid-mini">RFID: {{ $item->produto->codigo_rfid }}</span>
                                    @endif
                                </td>
                                <td style="text-align: center; font-weight: 700; color: #2081c3;">{{ $item->quantidade }}</td>
                                <td style="text-align: right;">R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                                <td style="text-align: right; font-weight: 700; color: #0f172a;">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="vazio-msg">
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="9" cy="21" r="1"></circle>
                                        <circle cx="20" cy="21" r="1"></circle>
                                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                    </svg>
                                    <p style="font-size: 1.05rem; font-weight: 600; margin-bottom: 4px;">Seu carrinho está vazio</p>
                                    <p style="font-size: 0.88rem; color: #94a3b8;">Coloque os produtos no carrinho. As antenas RFID identificarão cada item instantaneamente!</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Coluna Direita: Resumo Financeiro & Ofertas para Aumento de Ticket Médio -->
            <div class="sidebar-right">

                <!-- Card de Resumo Financeiro -->
                <div class="resumo-card">
                    <h2>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2081c3" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="5" width="20" height="14" rx="2"></rect>
                            <line x1="2" y1="10" x2="22" y2="10"></line>
                        </svg>
                        Resumo da Compra
                    </h2>

                    <div class="resumo-linha">
                        <span>Total de Itens</span>
                        <strong id="resumo-qtd">{{ $compra->itens->sum('quantidade') }} un</strong>
                    </div>

                    <div class="resumo-linha">
                        <span>Subtotal Líquido</span>
                        <strong id="resumo-subtotal">R$ {{ number_format(max(0, $compra->valor_total - $impostosAprox), 2, ',', '.') }}</strong>
                    </div>

                    <div class="resumo-linha impostos">
                        <span>Tributos Aprox. (Lei 12.741)</span>
                        <strong id="resumo-impostos">R$ {{ number_format($impostosAprox, 2, ',', '.') }}</strong>
                    </div>

                    <div class="resumo-total">
                        <span class="total-label">TOTAL:</span>
                        <span class="total-valor" id="total-carrinho">R$ {{ number_format($compra->valor_total, 2, ',', '.') }}</span>
                    </div>

                    <a href="/recibo/imprimir/{{ $compra->id_compra }}" class="btn-checkout">
                        <span>Concluir Compra</span>
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </a>
                </div>

                <!-- Card de Sugestões de Promoções (Pitch Item 4) -->
                @if(isset($promocoes) && $promocoes->count() > 0)
                    <div class="promo-card">
                        <h3>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                            Ofertas Especiais da Loja
                        </h3>
                        @foreach($promocoes as $promo)
                            <div class="promo-item">
                                <span class="promo-nome">{{ $promo->nome }}</span>
                                <span class="promo-preco">R$ {{ number_format($promo->preco, 2, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>

        </div>

    </div>

    <!-- Sincronização em Tempo Real (Polling IoT) -->
    <script>
        const ID_COMPRA_ATIVA = {{ $compra->id_compra }};

        function sincronizarCarrinho() {
            fetch(`/api/carrinho/${ID_COMPRA_ATIVA}`)
                .then(response => {
                    if (!response.ok) {
                        return fetch(`/carrinho/${ID_COMPRA_ATIVA}`).then(r => r.json());
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data || data.erro) return;

                    const tbody = document.getElementById('lista-itens');
                    let htmlAtualizado = '';

                    if (!data.itens || data.itens.length === 0) {
                        htmlAtualizado = `<tr>
                            <td colspan="4" class="vazio-msg">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="9" cy="21" r="1"></circle>
                                    <circle cx="20" cy="21" r="1"></circle>
                                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                                </svg>
                                <p style="font-size: 1.05rem; font-weight: 600; margin-bottom: 4px;">Seu carrinho está vazio</p>
                                <p style="font-size: 0.88rem; color: #94a3b8;">Coloque os produtos no carrinho. As antenas RFID identificarão cada item instantaneamente!</p>
                            </td>
                        </tr>`;
                    } else {
                        data.itens.forEach(item => {
                            const rfidTag = item.codigo_rfid ? `<span class="tag-rfid-mini">RFID: ${item.codigo_rfid}</span>` : '';
                            htmlAtualizado += `<tr>
                                <td>
                                    <div style="font-weight: 600; color: #0f172a;">${item.nome}</div>
                                    ${rfidTag}
                                </td>
                                <td style="text-align: center; font-weight: 700; color: #2081c3;">${item.quantidade}</td>
                                <td style="text-align: right;">R$ ${item.preco_unitario}</td>
                                <td style="text-align: right; font-weight: 700; color: #0f172a;">R$ ${item.subtotal}</td>
                            </tr>`;
                        });
                    }

                    tbody.innerHTML = htmlAtualizado;

                    // Atualiza resumo
                    if (data.quantidade_total !== undefined) {
                        document.getElementById('resumo-qtd').innerText = `${data.quantidade_total} un`;
                    }
                    if (data.subtotal_liquido !== undefined) {
                        document.getElementById('resumo-subtotal').innerText = `R$ ${data.subtotal_liquido}`;
                    }
                    if (data.impostos_total !== undefined) {
                        document.getElementById('resumo-impostos').innerText = `R$ ${data.impostos_total}`;
                    }
                    if (data.valor_total !== undefined) {
                        document.getElementById('total-carrinho').innerText = `R$ ${data.valor_total}`;
                    }
                })
                .catch(erro => console.warn('Aguardando sincronização do carrinho...', erro));
        }

        // Consulta a cada 1.5s
        setInterval(sincronizarCarrinho, 1500);
    </script>
</body>
</html>
