<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Stroller - Recibo de Compra</title>

    <!-- Gerador de Código de Barras -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.0/dist/JsBarcode.all.min.js"></script>

    <style>
        *{
            margin:0;
            padding:0;
            box-sizing:border-box;
            font-family:'Segoe UI', sans-serif;
        }

        body{
            min-height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:linear-gradient(135deg, white, #2081c3);
            color:white;
            padding:20px;
        }

        .recibo-container{
            width:100%;
            max-width:520px;
            padding:35px 30px;
            text-align:center;
            background:rgba(255,255,255,0.12);
            backdrop-filter:blur(12px);
            border-radius:20px;
            box-shadow:0 10px 30px rgba(0,0,0,0.25);
            animation:aparecer 0.8s ease;
        }

        h1{
            font-size:2.3rem;
            margin-bottom:4px;
        }

        p.subtitulo{
            font-size:0.95rem;
            opacity:0.9;
            margin-bottom:20px;
        }

        .status-tag {
            display: inline-block;
            background: #28a745;
            color: white;
            font-weight: bold;
            font-size: 0.85rem;
            padding: 5px 15px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }

        .cupom-papel {
            background: #fffdf7;
            color: #222;
            border-radius: 12px;
            padding: 22px;
            text-align: left;
            font-family: 'Courier New', Courier, monospace;
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
            margin-bottom: 25px;
        }

        .cupom-header {
            text-align: center;
            border-bottom: 1px dashed #666;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .cupom-header h2 {
            font-size: 1.2rem;
            font-weight: bold;
        }

        .cupom-info {
            font-size: 0.85rem;
            line-height: 1.4;
            border-bottom: 1px dashed #666;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .tabela-itens {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.85rem;
            margin-bottom: 12px;
        }

        .tabela-itens th {
            border-bottom: 1px solid #333;
            padding-bottom: 4px;
            text-align: left;
        }

        .tabela-itens td {
            padding: 5px 0;
            vertical-align: top;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .total-box {
            border-top: 2px dashed #333;
            border-bottom: 2px dashed #333;
            padding: 8px 0;
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
            font-size: 1.05rem;
            font-weight: bold;
        }

        .barcode-container {
            text-align: center;
            margin-top: 15px;
        }

        .barcode-container svg {
            max-width: 100%;
            height: 45px;
        }

        .botoes-grupo {
            display: flex;
            gap: 12px;
        }

        button, a.btn-link {
            flex: 1;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-imprimir {
            background: white;
            color: #2081c3;
        }

        .btn-imprimir:hover {
            transform: translateY(-2px);
            background: #f0f0f0;
        }

        .btn-inicio {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.4);
        }

        .btn-inicio:hover {
            transform: translateY(-2px);
            background: rgba(255, 255, 255, 0.3);
        }

        @keyframes aparecer{
            from{
                opacity:0;
                transform:translateY(30px);
            }
            to{
                opacity:1;
                transform:translateY(0);
            }
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .recibo-container {
                background: white;
                box-shadow: none;
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
            <p class="subtitulo">Compra Concluída com Sucesso!</p>
        </header>

        <div class="status-tag">
            Status: {{ $compra->status }}
        </div>

        <div class="cupom-papel">
            <div class="cupom-header">
                <h2>SMART STROLLER</h2>
                <p>Comprovante de Compra #{{ $compra->id_compra }}</p>
            </div>

            <div class="cupom-info">
                <p><strong>Cliente:</strong> {{ $compra->usuario->nome ?? 'Não informado' }}</p>
                @if(!empty($compra->usuario->cpf))
                    <p><strong>CPF:</strong> {{ $compra->usuario->cpf }}</p>
                @endif
                <p><strong>E-mail:</strong> {{ $compra->usuario->email ?? '-' }}</p>
                <p><strong>Data:</strong> {{ \Carbon\Carbon::parse($compra->data_fim ?? now())->format('d/m/Y H:i:s') }}</p>
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
                            <td>{{ $item->produto->nome }}</td>
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

            <div class="total-box">
                <span>TOTAL A PAGAR:</span>
                <span>R$ {{ number_format($compra->valor_total, 2, ',', '.') }}</span>
            </div>

            <div class="barcode-container">
                <svg id="barcode"></svg>
            </div>
        </div>

        <div class="botoes-grupo">
            <button onclick="window.print()" class="btn-imprimir">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 6px;">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Imprimir
            </button>
            <a href="/" class="btn-link btn-inicio">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-right: 6px;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                Nova Compra
            </a>
        </div>
    </div>

    <script>
        JsBarcode("#barcode", "STROLLER-{{ $compra->id_compra }}", {
            format: "CODE128",
            lineColor: "#000",
            width: 2,
            height: 40,
            displayValue: true,
            fontSize: 12
        });
    </script>

</body>
</html>