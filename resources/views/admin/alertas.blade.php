@extends('admin.index')
@section('conteudo')
<section id="alertas" class="section active">
    <div class="top"><h1>Central de Notificações</h1></div>
    <div id="listaAlertas">
        <div class="box" style="display: flex; align-items: center; gap: 10px;">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <p style="margin: 0;">Nenhum problema crítico detectado no momento.</p>
        </div>
    </div>
    <div class="alertBox" id="alertaCritico" style="display: none; align-items: center; gap: 10px;">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
        <span><strong>ALERTA:</strong> Existem itens marcados como "Faltando" que precisam de atenção da reposição!</span>
    </div>
</section>
@endsection