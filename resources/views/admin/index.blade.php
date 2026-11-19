
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmartStroller - Sistema Inteligente</title>
    <link rel="stylesheet" href="/admin.css">
</head>

<body>

    <div class="sidebar">
        <div class="logo">SmartStroller</div>
        <div class="menu">
            <a href="/admin" id="btn-dashboard" class="active">Dashboard</a>
            <a href="/admin/produtos" id="btn-produtos">Produtos</a>
            <a href="/admin/alertas" id="btn-alertas">Alertas</a>
            <a href="/admin/clientes" id="btn-clientes">Clientes</a>
            <a href="/admin/compras" id="btn-compras">Compras</a>
        </div>
            <style>
            .logout-wrapper {
                position: fixed;
                bottom: 24px;
                left: 24px;
                z-index: 999;
            }

            .btn-logout {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background-color: #ef4444;
                color: #ffffff;
                border-radius: 50px;
                font-size: 15px;
                font-weight: 600;
                cursor: pointer;
                text-decoration: none;
                box-shadow: 0 4px 14px rgba(239, 68, 68, 0.4);
                transition: transform 0.2s ease, background-color 0.2s ease, box-shadow 0.2s ease;
            }

            .btn-logout:hover {
                background-color: #dc2626;
                box-shadow: 0 6px 20px rgba(220, 38, 38, 0.5);
                transform: translateY(-2px);
                color: #ffffff;
            }

            .btn-logout:active {
                transform: translateY(0);
            }
        </style>

        <div class="logout-wrapper">
            <a href="/" class="btn-logout">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0z"/>
                    <path fill-rule="evenodd" d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708z"/>
                </svg>
                Sair
            </a>
        </div>
    </div>

    
    <div class='main'>
        @yield('conteudo')
    </div>
</body>
</html>