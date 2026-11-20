<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Stroller - Identificação do Cliente</title>

    <style>
        /* Corrigido de "-" para "*" */
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

        .login-container {
            width: 100%;
            max-width: 520px;
            padding: 35px 38px;
            text-align: center;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.35);
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.22);
            animation: aparecer 0.7s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
        }

        .btn-voltar {
            position: absolute;
            top: 20px;
            left: 20px;
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: 0.2s;
        }

        .btn-voltar:hover {
            color: #ffffff;
            transform: translateX(-2px);
        }

        header {
            margin-top: 10px;
            margin-bottom: 22px;
        }

        h1 {
            font-size: 2.4rem;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        p.subtitulo {
            font-size: 1rem;
            opacity: 0.92;
            color: #e2e8f0;
        }

        /* Seletor de Abas (Tabs) */
        .tabs-nav {
            display: flex;
            background: rgba(0, 0, 0, 0.18);
            border-radius: 14px;
            padding: 4px;
            margin-bottom: 24px;
            gap: 4px;
        }

        .tab-btn {
            flex: 1;
            padding: 11px 14px;
            border: none;
            background: transparent;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.95rem;
            font-weight: 600;
            border-radius: 11px;
            cursor: pointer;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .tab-btn.active {
            background: #ffffff;
            color: #2081c3;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .tab-btn:hover:not(.active) {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.1);
        }

        /* Grupos de Formulário */
        .form-pane {
            display: none;
            animation: fadeIn 0.35s ease;
        }

        .form-pane.active {
            display: block;
        }

        .input-group {
            text-align: left;
            margin-bottom: 16px;
        }

        label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
            font-weight: 600;
            font-size: 0.92rem;
            color: #ffffff;
        }

        .tag-opcional {
            font-size: 0.75rem;
            font-weight: normal;
            opacity: 0.8;
            background: rgba(255, 255, 255, 0.2);
            padding: 2px 8px;
            border-radius: 6px;
        }

        input {
            width: 100%;
            padding: 13px 16px;
            border: 1px solid rgba(255, 255, 255, 0.35);
            border-radius: 12px;
            font-size: 1rem;
            outline: none;
            background: #ffffff;
            color: #1e293b;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input:focus {
            border-color: #06b6d4;
            box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.35);
        }

        input::placeholder {
            color: #94a3b8;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 12px;
            background: #ffffff;
            color: #2081c3;
            font-size: 1.08rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            margin-top: 8px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.15);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            background: #f8fafc;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        /* Alertas de Erro */
        .alert-erro {
            background: rgba(239, 68, 68, 0.9);
            color: #ffffff;
            padding: 12px 16px;
            border-radius: 12px;
            margin-bottom: 20px;
            font-size: 0.92rem;
            text-align: left;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            display: flex;
            align-items: flex-start;
            gap: 10px;
            animation: shake 0.4s ease;
        }

        .alert-erro ul {
            margin-left: 18px;
            margin-top: 4px;
        }

        @keyframes aparecer {
            from {
                opacity: 0;
                transform: translateY(25px) scale(0.97);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            20%, 60% { transform: translateX(-6px); }
            40%, 80% { transform: translateX(6px); }
        }
    </style>
</head>

<body>

    <div class="login-container">
        <a href="/" class="btn-voltar">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Início
        </a>

        <header>
            <h1>Smart Stroller</h1>
            <p class="subtitulo">Identifique-se para iniciar seu carrinho</p>
        </header>

        <!-- Mensagens de Alerta / Erro -->
        @if(session('erro'))
            <div class="alert-erro">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 1px;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div>{{ session('erro') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="alert-erro">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; margin-top: 1px;">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Navegação de Abas -->
        @php
            $abaPadrao = session('aba_ativa', old('tipo_acesso', 'login'));
        @endphp

        <div class="tabs-nav">
            <button type="button" 
                    class="tab-btn {{ $abaPadrao === 'login' ? 'active' : '' }}" 
                    id="btn-tab-login" 
                    onclick="alternarAba('login')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                Já sou cadastrado
            </button>
            
            <button type="button" 
                    class="tab-btn {{ $abaPadrao === 'cadastro' ? 'active' : '' }}" 
                    id="btn-tab-cadastro" 
                    onclick="alternarAba('cadastro')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="8.5" cy="7" r="4"></circle>
                    <line x1="20" y1="8" x2="20" y2="14"></line>
                    <line x1="23" y1="11" x2="17" y2="11"></line>
                </svg>
                Primeiro acesso
            </button>
            
            <button type="button" 
                    class="tab-btn {{ $abaPadrao === 'admin' ? 'active' : '' }}" 
                    id="btn-tab-admin" 
                    onclick="alternarAba('admin')">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                Admin
            </button>
        </div>

        <!-- ==============================================
             ABA 1: CLIENTE JÁ CADASTRADO (LOGIN POR NOME E CPF)
             ============================================== -->
        <div id="pane-login" class="form-pane {{ $abaPadrao === 'login' ? 'active' : '' }}">
            <form action="/compra/iniciar" method="POST">
                @csrf
                <input type="hidden" name="tipo_acesso" value="login">
                <input type="hidden" name="id_stroller" value="{{ request('stroller_id', 1) }}">

                <div class="input-group">
                    <label for="login_nome">Nome cadastrado</label>
                    <input
                        type="text"
                        id="login_nome"
                        name="nome"
                        placeholder="Ex: Alefe Henrique"
                        value="{{ old('tipo_acesso') === 'login' ? old('nome') : '' }}"
                        required>
                </div>

                <div class="input-group">
                    <label for="login_cpf">CPF</label>
                    <input
                        type="text"
                        id="login_cpf"
                        name="cpf"
                        class="mask-cpf"
                        placeholder="000.000.000-00"
                        maxlength="14"
                        value="{{ old('tipo_acesso') === 'login' ? old('cpf') : '' }}"
                        required>
                </div>

                <button type="submit" class="btn-submit">
                    <span>Acessar Carrinho</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>
        </div>

        <!-- ==============================================
             ABA 2: NOVO CLIENTE (CADASTRO COMPLETO)
             ============================================== -->
        <div id="pane-cadastro" class="form-pane {{ $abaPadrao === 'cadastro' ? 'active' : '' }}">
            <form action="/compra/iniciar" method="POST">
                @csrf
                <input type="hidden" name="tipo_acesso" value="cadastro">
                <input type="hidden" name="id_stroller" value="{{ request('stroller_id', 1) }}">

                <div class="input-group">
                    <label for="cad_nome">Qual é o seu nome completo?</label>
                    <input
                        type="text"
                        id="cad_nome"
                        name="nome"
                        placeholder="Digite seu nome completo..."
                        value="{{ old('tipo_acesso') === 'cadastro' ? old('nome') : '' }}"
                        required>
                </div>

                <div class="input-group">
                    <label for="cad_cpf">CPF</label>
                    <input
                        type="text"
                        id="cad_cpf"
                        name="cpf"
                        class="mask-cpf"
                        placeholder="000.000.000-00"
                        maxlength="14"
                        value="{{ old('tipo_acesso') === 'cadastro' ? old('cpf') : '' }}"
                        required>
                </div>

                <div class="input-group">
                    <label for="cad_email">
                        E-mail
                        <span class="tag-opcional">Opcional</span>
                    </label>
                    <input
                        type="email"
                        id="cad_email"
                        name="email"
                        placeholder="seu.email@exemplo.com"
                        value="{{ old('tipo_acesso') === 'cadastro' ? old('email') : '' }}">
                </div>

                <div class="input-group">
                    <label for="cad_telefone">
                        Telefone / WhatsApp
                        <span class="tag-opcional">Opcional</span>
                    </label>
                    <input
                        type="tel"
                        id="cad_telefone"
                        name="telefone"
                        class="mask-tel"
                        placeholder="(00) 00000-0000"
                        maxlength="15"
                        value="{{ old('tipo_acesso') === 'cadastro' ? old('telefone') : '' }}">
                </div>

                <button type="submit" class="btn-submit">
                    <span>Cadastrar e Iniciar</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>
        </div>

        <!-- ==============================================
             ABA 3: LOGIN DO ADMINISTRADOR
             ============================================== -->
        <div id="pane-admin" class="form-pane {{ $abaPadrao === 'admin' ? 'active' : '' }}">
            <form action="/login" method="POST">
                @csrf
                
                <div class="input-group">
                    <label for="admin_login">Login</label>
                    <input type="text" id="admin_login" name="login" placeholder="admin" required>
                </div>
                
                <!-- Adicionado o campo de senha que estava faltando -->
                <div class="input-group">
                    <label for="admin_senha">Senha</label>
                    <input type="password" id="admin_senha" name="senha" placeholder="******" required>
                </div>
                
                <button type="submit" class="btn-submit">
                    <span>Acessar Admin</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"></line>
                        <polyline points="12 5 19 12 12 19"></polyline>
                    </svg>
                </button>
            </form>
        </div>

    </div>

    <!-- Scripts de Interatividade e Máscaras -->
    <script>
        function alternarAba(tipo) {
            // Atualiza botões
            document.getElementById('btn-tab-login').classList.toggle('active', tipo === 'login');
            document.getElementById('btn-tab-cadastro').classList.toggle('active', tipo === 'cadastro');
            document.getElementById('btn-tab-admin').classList.toggle('active', tipo === 'admin');

            // Atualiza formulários
            document.getElementById('pane-login').classList.toggle('active', tipo === 'login');
            document.getElementById('pane-cadastro').classList.toggle('active', tipo === 'cadastro');
            document.getElementById('pane-admin').classList.toggle('active', tipo === 'admin');
        }

        // Máscara de CPF automática (000.000.000-00)
        document.querySelectorAll('.mask-cpf').forEach(input => {
            input.addEventListener('input', e => {
                let v = e.target.value.replace(/\D/g, '');
                if (v.length > 11) v = v.slice(0, 11);
                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})(\d)/, '$1.$2');
                v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
                e.target.value = v;
            });
        });

        // Máscara de Telefone automática ((00) 00000-0000)
        document.querySelectorAll('.mask-tel').forEach(input => {
            input.addEventListener('input', e => {
                let v = e.target.value.replace(/\D/g, '');
                if (v.length > 11) v = v.slice(0, 11);
                if (v.length > 10) {
                    v = v.replace(/^(\d{2})(\d{5})(\d{4})$/, '($1) $2-$3');
                } else if (v.length > 5) {
                    v = v.replace(/^(\d{2})(\d{4})(\d{0,4})$/, '($1) $2-$3');
                } else if (v.length > 2) {
                    v = v.replace(/^(\d{2})(\d{0,5})$/, '($1) $2');
                } else {
                    v = v.replace(/^(\d*)$/, '$1');
                }
                e.target.value = v;
            });
        });
    </script>

</body>
</html>