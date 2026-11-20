<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Stroller - Cupom de Checkout Express</title>

    <!-- Gerador de Código de Barras Code128 -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #ffffff, #2081c3);
            color: #ffffff;
            padding: 20px;
        }

        .recibo-container {
            width: 100%;
            max-width: 480px;
            padding: 35px 28px;
            text-align: center;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(14px);
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.22);
            border: 1px solid rgba(255, 255, 255, 0.35);
            animation: aparecer 0.7s cubic-bezier(0.16, 1, 0.3, 1);
        }

        h1 {
            font-size: 2.2rem;
            margin-bottom: 4px;
            color: #ffffff;
            font-weight: 800;
        }

        p.subtitulo {
            font-size: 0.95rem;
            color: #e2e8f0;
            margin-bottom: 16px;
        }

        .status-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #10b981;
            color: #ffffff;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 6px 14px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }

        .cupom-papel {
            background: #fffdfa;
            color: #1e293b;
            border-radius: 14px;
            padding: 24px;
            text-align: left;
            font-family: 'Courier New', Courier, monospace;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            margin-bottom: 24px;
            border: 1px solid #f1f5f9;
        }

        .cupom-header {
            text-align: center;
            border-bottom: 1px dashed #94a3b8;
            padding-bottom: 12px;
            margin-bottom: 14px;
        }

        .cupom-header h2 {
            font-size: 1.3rem;
            font-weight: 900;
            letter-spacing: 1px;
            color: #0f172a;
        }

        .cupom-info {
            font-size: 0.82rem;
            line-height: 1.5;
            border-bottom: 1px dashed #94a3b8;
            padding-bottom: 12px;
            margin-bottom: 14px;
            color: #334155;
        }

        .tabela-itens {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.82rem;
            margin-bottom: 14px;
        }

        .tabela-itens th {
            border-bottom: 1px solid #0f172a;
            padding-bottom: 6px;
            text-align: left;
            color: #0f172a;
        }

        .tabela-itens td {
            padding: 6px 0;
            vertical-align: top;
            color: #1e293b;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .tax-box {
            font-size: 0.78rem;
            color: #64748b;
            padding: 6px 0;
            border-top: 1px dotted #cbd5e1;
            margin-top: 6px;
            display: flex;
            justify-content: space-between;
        }

        .total-box {
            border-top: 2px dashed #0f172a;
            border-bottom: 2px dashed #0f172a;
            padding: 10px 0;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            font-size: 1.15rem;
            font-weight: 900;
            color: #0f172a;
        }

        .barcode-container {
            text-align: center;
            margin-top: 18px;
            padding-top: 10px;
            border-top: 1px dashed #94a3b8;
        }

        .barcode-container svg {
            max-width: 100%;
            height: 48px;
        }

        .instrucao-caixa {
            font-size: 0.75rem;
            color: #64748b;
            text-align: center;
            margin-top: 8px;
            line-height: 1.3;
        }

        .botoes-grupo {
            display: flex;
            gap: 12px;
        }

        button, a.btn-link {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 12px;
            font-size: 0.98rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .btn-imprimir {
            background: #ffffff;
            color: #2081c3;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .btn-imprimir:hover {
            transform: translateY(-2px);
            background: #f8fafc;
        }

        .btn-inicio {
            background: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .btn-inicio:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.3);
        }

        @keyframes aparecer {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .recibo-container {
                background: #ffffff;
                box-shadow: none;
                border: none;
                padding: 0;
                max-width: 100%;
            }
            .botoes-grupo, header, .status-tag {
                display: none !important;
            }
            .cupom-papel {
                box-shadow: none;
                border: none;
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="recibo-container">
        <header>
            <h1>Smart Stroller</h1>
            <p class="subtitulo">Checkout Consolidado AGGLY</p>
        </header>

        <div class="status-tag">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            Status: {{ $compra->status }}
        </div>

        <div class="cupom-papel">
            <div class="cupom-header">
                <h2>SMART STROLLER AGGLY</h2>
                <p>CUPOM DE CHECKOUT EXPRESS #{{ str_pad($compra->id_compra, 4, '0', STR_PAD_LEFT) }}</p>
            </div>

            <div class="cupom-info">
                <p><strong>Dispositivo:</strong> {{ $compra->stroller->numero_identificacao ?? 'STROLLER-01' }}</p>
                <p><strong>Cliente:</strong> {{ $compra->usuario->nome ?? 'Cliente Não Informado' }}</p>
                @if(!empty($compra->usuario->cpf))
                    <p><strong>CPF:</strong> {{ $compra->usuario->cpf }}</p>
                @endif
                <p><strong>Data/Hora:</strong> {{ \Carbon\Carbon::parse($compra->data_fim ?? now())->format('d/m/Y H:i:s') }}</p>
                <p><strong>Validação Antifraude:</strong> Verificado (Câmera + RFID)</p>
            </div>

            <table class="tabela-itens">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="text-center">Qtd</th>
                        <th class="text-right">R$ Un</th>
                        <th class="text-right">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compra->itens as $item)
                        <tr>
                            <td>{{ $item->produto->nome ?? 'Item' }}</td>
                            <td class="text-center">{{ $item->quantidade }}</td>
                            <td class="text-right">{{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                            <td class="text-right">{{ number_format($item->subtotal, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Nenhum item registrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @php
                $tributos = $impostosAprox ?? round(((float)$compra->valor_total) * 0.185, 2);
            @endphp
            <div class="tax-box">
                <span>Tributos Aprox. (Lei Fed. 12.741/12):</span>
                <span>R$ {{ number_format($tributos, 2, ',', '.') }} (~18,5%)</span>
            </div>

            <div class="total-box">
                <span>TOTAL A PAGAR:</span>
                <span>R$ {{ number_format($compra->valor_total, 2, ',', '.') }}</span>
            </div>

            <div class="barcode-container">
                <svg id="barcode"></svg>
                <p class="instrucao-caixa">
                    Apresente este código no Caixa Express.<br>
                    O operador escaneará uma única vez e efetuará o pagamento em segundos.
                </p>
            </div>
        </div>

        <div class="botoes-grupo">
            <button onclick="window.print()" class="btn-imprimir">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Imprimir Cupom
            </button>
            <a href="/" class="btn-link btn-inicio">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Início
            </a>
        </div>
    </div>

    <script>
        JsBarcode("#barcode", "STROLLER-{{ $compra->id_compra }}", {
            format: "CODE128",
            lineColor: "#0f172a",
            width: 2,
            height: 45,
            displayValue: true,
            fontSize: 12,
            font: "Courier New"
        });
    </script>

</body>
</html>
