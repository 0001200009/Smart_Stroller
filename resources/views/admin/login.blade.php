<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistema</title>
    <style>
        /* CSS Base Fornecido */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Arial, sans-serif;
        }

        body {
            background: #0f172a;
            color: white;
            display: flex;
            height: 100vh;
            overflow: hidden;
            justify-content: center;
            align-items: center;
        }

        .box {
            background: #1e293b;
            padding: 25px;
            border-radius: 20px;
            margin-bottom: 25px;
            border: 1px solid #334155;
        }

        .logo {
            font-size: 26px;
            font-weight: bold;
            margin-bottom: 40px;
            color: #06b6d4;
            text-align: center;
        }

        input {
            width: 100%; padding: 12px; margin-bottom: 5px;
            border-radius: 8px; background: #0f172a; color: white; border: 1px solid #334155;
            outline: none;
        }

        input:focus {
            border-color: #06b6d4;
        }

        .addBtn {
            width: 100%; padding: 15px; border-radius: 8px;
            background: #06b6d4; color: white; font-weight: bold; cursor: pointer; border: none;
            transition: 0.3s;
            margin-top: 15px;
        }

        .addBtn:hover {
            background: #0891b2;
        }

        /* Estilos Específicos para o Login */
        .login-card {
            width: 100%;
            max-width: 400px;
        }

        .field-label {
            display: block;
            margin-bottom: 8px;
            color: #94a3b8;
            font-size: 14px;
        }

        .input-group {
            margin-bottom: 15px;
        }

        .password-container {
            position: relative;
        }

        .password-container input {
            padding-right: 85px; 
        }

        .toggle-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: #1e293b;
            color: #06b6d4;
            border: 1px solid #334155;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .toggle-btn:hover {
            background: #334155;
            color: white;
        }

        /* Texto vermelho de erro oculto por padrão */
        .error-text {
            color: #f87171;
            font-size: 13px;
            margin-top: 5px;
            display: none; 
        }
    </style>
</head>
<body>

    <div class="login-card box">
        <div class="logo">ÁREA ADMINISTRATIVA</div>
        
        <form id="loginForm">
            <div class="input-group">
                <label class="field-label" for="login">Login</label>
                <input type="text" id="login" placeholder="Digite seu usuário">
                <span class="error-text" id="erroLogin">Login incorreto.</span>
            </div>

            <div class="input-group">
                <label class="field-label" for="senha">Senha</label>
                <div class="password-container">
                    <input type="password" id="senha" placeholder="Digite sua senha">
                    <button type="button" class="toggle-btn" id="toggleBtn" onclick="togglePassword()">Mostrar</button>
                </div>
                <span class="error-text" id="erroSenha">Senha incorreta.</span>
            </div>

            <button type="submit" class="addBtn">Acessar Painel</button>
        </form>
    </div>

    <script>
        // Funcionalidade para mostrar/ocultar senha
        function togglePassword() {
            const senhaInput = document.getElementById('senha');
            const toggleBtn = document.getElementById('toggleBtn');
            
            if (senhaInput.type === 'password') {
                senhaInput.type = 'text';
                toggleBtn.textContent = 'Ocultar';
            } else {
                senhaInput.type = 'password';
                toggleBtn.textContent = 'Mostrar';
            }
        }

        // Validação front-end simulando a lógica pedida
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Impede o envio real do formulário

            // Dados corretos simulados do sistema
            const loginCorreto = "admin";
            const senhaCorreta = "123456";

            // Valores digitados pelo usuário
            const loginDigitado = document.getElementById('login').value;
            const senhaDigitada = document.getElementById('senha').value;

            // Elementos de erro
            const erroLogin = document.getElementById('erroLogin');
            const erroSenha = document.getElementById('erroSenha');

            // Resetando mensagens de erro
            erroLogin.style.display = 'none';
            erroSenha.style.display = 'none';

            let temErro = false;

            // Verificando os dados
            if (loginDigitado !== loginCorreto) {
                erroLogin.style.display = 'block';
                temErro = true;
            }
            if (senhaDigitada !== senhaCorreta) {
                erroSenha.style.display = 'block';
                temErro = true;
            }

            // Se os dois estiverem corretos, redireciona
            if (!temErro) {
                window.location.href = "/admin";
            }
        });
    </script>
</body>
</html>