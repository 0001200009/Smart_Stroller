<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Bem-vindo</title>

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: 'Segoe UI', sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg, white, #2081c3);
    color:black;
    text-align:center;
    cursor:pointer;
    overflow:hidden;
}

.container{
    max-width:700px;
    padding:40px;
    animation: aparecer 1s ease;
}

h1{
    font-size:4rem;
    margin-bottom:20px;
}

p{
    font-size:1.3rem;
    opacity:0.9;
    margin-bottom:30px;
}

.clique{
    display:inline-block;
    padding:15px 30px;
    border:2px solid rgba(255,255,255,0.4);
    border-radius:50px;
    backdrop-filter:blur(10px);
    background:rgba(255,255,255,0.1);
    transition:0.3s;
}

.clique:hover{
    background:rgba(255,255,255,0.2);
    transform:scale(1.05);
}
.btn-admin {
  /* Posicionamento no canto inferior esquerdo */
  position: fixed;
  bottom: 20px;
  left: 20px;
  z-index: 9999; /* Garante que o botão fique sobreposto a outros elementos */

  /* Estilização visual */
  padding: 10px 16px;
  background-color: #333333; /* Mude para a cor principal do seu site */
  color: #ffffff; /* Cor do texto */
  font-family: inherit; /* Herda a fonte do seu site */
  font-size: 14px;
  font-weight: 500;
  text-decoration: none;
  border-radius: 6px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
  
  /* Transição suave para o efeito de hover */
  transition: all 0.3s ease;
}

.btn-admin:hover {
  background-color: #555555; /* Cor do botão ao passar o mouse */
  box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2);
  transform: translateY(-2px); /* Leve efeito de elevação */
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
</style>
</head>

<body>

<div class="container">
    <h1>Bem-vindo</h1>
    <div class="clique">
        Clique para entrar
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: middle; margin-left: 6px;">
            <line x1="5" y1="12" x2="19" y2="12"></line>
            <polyline points="12 5 19 12 12 19"></polyline>
        </svg>
    </div>
</div>
<a href="/login" class="btn-admin">Entrar como Administrador</a>

<script>
document.addEventListener("click", (e) => {
    // Não intercepta se o clique for no botão de administrador
    if (e.target.closest('.btn-admin')) {
        return;
    }
    window.location.href = "/sistema";
});
</script>

</body>
</html>