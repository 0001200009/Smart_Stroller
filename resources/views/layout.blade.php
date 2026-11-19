<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Stroller - Carrinho de Compras</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            min-height: 100vh;
            background:linear-gradient(135deg, white, #2081c3);
            color: white;
            padding: 30px 20px;
            display: flex;
            justify-content: center;
        }

        .layout-container {
            width: 100%;
            max-width: 900px;
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        h1 { font-size: 1.8rem; }
        .comprador-tag { font-size: 1.1rem; opacity: 0.9; }

        .tabela-produtos {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .tabela-produtos th {
            text-align: left;
            padding: 12px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 8px 8px 0 0;
        }

        .tabela-produtos td {
            padding: 14px 12px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .resumo-box {
            background: rgba(255, 255, 255, 0.15);
            padding: 20px;
            border-radius: 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-texto { font-size: 1.5rem; font-weight: bold; }

        .btn-finalizar {
            background: white;
            color: #2081c3;
            padding: 14px 28px;
            border-radius: 10px;
            font-weight: bold;
            font-size: 1.1rem;
            text-decoration: none;
            transition: 0.3s;
            display: inline-block;
        }

        .btn-finalizar:hover {
            background: #f0f0f0;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="layout-container">
        <header>
            <div>
                <h1> Smart Stroller</h1>
                <p>Carrinho #{{ $compra->id_compra }}</p>
            </div>
            <div class="comprador-tag">
                 Olá, <strong>{{ $nomeComprador }}</strong>!
            </div>
        </header>

        <!-- Tabela de Itens Adicionados -->
        <table class="tabela-produtos">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th style="text-align: center;">Qtd</th>
                    <th style="text-align: right;">Preço Un.</th>
                    <th style="text-align: right;">Subtotal</th>
                </tr>
            </thead>
            <tbody id="lista-itens">
                <!-- O conteúdo inicial será renderizado pelo Blade e depois assumido pelo JS -->
                @forelse($compra->itens as $item)
                    <tr>
                        <td>{{ $item->produto->nome }}</td>
                        <td style="text-align: center;">{{ $item->quantidade }}</td>
                        <td style="text-align: right;">R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</td>
                        <td style="text-align: right;">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; opacity: 0.7; padding: 30px;">
                            Nenhum produto no carrinho ainda. Passe os itens no leitor!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Resumo e Botão de Ação -->
        <div class="resumo-box">
            <div class="total-texto" id="total-carrinho">
                Total: R$ {{ number_format($compra->valor_total, 2, ',', '.') }}
            </div>

            <!-- Botão que direciona para o Controller emitir o Recibo e alterar status para Concluida -->
            <a href="/recibo/imprimir/{{ $compra->id_compra }}" class="btn-finalizar">
                Finalizar Compra
            </a>
        </div>
    </div>

    <!-- Script para integrações em tempo real via JS -->
    <script>
        const ID_COMPRA_ATIVA = {{ $compra->id_compra }};

        // Função responsável por buscar os dados no banco e atualizar o layout continuamente
        function sincronizarCarrinho() {
            // Usa a nova rota criada (ajuste o '/api/carrinho/' se tiver colocado em web.php)
            fetch(`/api/carrinho/${ID_COMPRA_ATIVA}`)
                .then(response => response.json())
                .then(data => {
                    if(data.erro) return;

                    const tbody = document.getElementById('lista-itens');
                    let htmlAtualizado = '';

                    // Se não houver itens, mostra a mensagem vazia
                    if (data.itens.length === 0) {
                        htmlAtualizado = `<tr>
                            <td colspan="4" style="text-align: center; opacity: 0.7; padding: 30px;">
                                Nenhum produto no carrinho ainda. Passe os itens no leitor!
                            </td>
                        </tr>`;
                    } else {
                        // Constrói as linhas da tabela dinamicamente com os produtos retornados
                        data.itens.forEach(item => {
                            htmlAtualizado += `<tr>
                                <td>${item.nome}</td>
                                <td style="text-align: center;">${item.quantidade}</td>
                                <td style="text-align: right;">R$ ${item.preco_unitario}</td>
                                <td style="text-align: right;">R$ ${item.subtotal}</td>
                            </tr>`;
                        });
                    }

                    // Atualiza a tabela de produtos
                    tbody.innerHTML = htmlAtualizado;
                    // Atualiza o total
                    document.getElementById('total-carrinho').innerText = `Total: R$ ${data.valor_total}`;
                })
                .catch(erro => console.error('Erro de sincronização com o banco:', erro));
        }

        // Executa a função sincronizarCarrinho a cada 1.5 segundos
        setInterval(sincronizarCarrinho, 1500);
    </script>
</body>
</html>