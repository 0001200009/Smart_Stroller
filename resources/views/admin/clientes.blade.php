@extends('admin.index')

@section('conteudo')
<section id="clientes" class="section active">
    <div class="top" style="margin-bottom: 25px;">
        <h1 style="font-size: 26px; font-weight: 700;">Gestão da Base de Clientes</h1>
        <p style="color: #94a3b8; font-size: 14px; margin-top: 4px;">Cadastro de clientes para autoatendimento e login ágil no totem dos carrinhos inteligentes</p>
    </div>

    <!-- Formulário de Adicionar / Editar Cliente -->
    <form class="box" method="POST" action="{{ isset($editarCliente) ? '/admin/clientes/salvar/'.$editarCliente->id_usuario : '/admin/clientes/salvar' }}">
        @csrf
        @if(isset($editarCliente))
            @method('PUT')
        @endif

        <h2 style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                <circle cx="12" cy="7" r="4"></circle>
            </svg>
            {{ isset($editarCliente) ? 'Editar Cliente: '.$editarCliente->nome : 'Cadastrar Novo Cliente' }}
        </h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 15px;">
            <div>
                <label style="display: block; font-size: 13px; color: #94a3b8; margin-bottom: 5px;">Nome Completo *</label>
                <input type="text" name="nome" id="nome" placeholder="Ex: Alefe Henrique" value="{{ $editarCliente->nome ?? old('nome') }}" required>
            </div>

            <div>
                <label style="display: block; font-size: 13px; color: #94a3b8; margin-bottom: 5px;">CPF *</label>
                <input type="text" name="cpf" id="cpf" class="mask-cpf" placeholder="000.000.000-00" maxlength="14" value="{{ $editarCliente->cpf ?? old('cpf') }}" required>
            </div>

            <div>
                <label style="display: block; font-size: 13px; color: #94a3b8; margin-bottom: 5px;">E-mail</label>
                <input type="email" name="email" id="email" placeholder="cliente@exemplo.com" value="{{ $editarCliente->email ?? old('email') }}">
            </div>

            <div>
                <label style="display: block; font-size: 13px; color: #94a3b8; margin-bottom: 5px;">Telefone / WhatsApp</label>
                <input type="tel" name="telefone" id="telefone" class="mask-tel" placeholder="(00) 00000-0000" maxlength="15" value="{{ $editarCliente->telefone ?? old('telefone') }}">
            </div>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="addBtn" style="flex: 1;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                {{ isset($editarCliente) ? 'Salvar Alterações' : 'Adicionar Cliente' }}
            </button>
            @if(isset($editarCliente))
                <a href="/admin/clientes" class="btn btn-secondary" style="padding: 12px 20px;">Cancelar</a>
            @endif
        </div>
    </form>

    <!-- Tabela de Clientes -->
    <div class="box">
        <h2 style="display: flex; align-items: center; justify-content: space-between;">
            <span>Clientes Cadastrados ({{ $clientes->count() }})</span>
        </h2>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                <thead>
                    <tr style="border-bottom: 2px solid #334155; color: #94a3b8;">
                        <th style="padding: 12px 8px;">ID</th>
                        <th style="padding: 12px 8px;">Nome</th>
                        <th style="padding: 12px 8px;">CPF</th>
                        <th style="padding: 12px 8px;">E-mail</th>
                        <th style="padding: 12px 8px;">Telefone</th>
                        <th style="padding: 12px 8px; text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($clientes as $cliente)
                    <tr style="border-bottom: 1px solid #1e293b;">
                        <td style="padding: 12px 8px; color: #64748b;">#{{ $cliente->id_usuario }}</td>
                        <td style="padding: 12px 8px; font-weight: 600; color: #ffffff;">{{ $cliente->nome }}</td>
                        <td style="padding: 12px 8px; font-family: monospace; color: #06b6d4;">{{ $cliente->cpf ?? '-' }}</td>
                        <td style="padding: 12px 8px; color: #94a3b8;">{{ $cliente->email ?? '-' }}</td>
                        <td style="padding: 12px 8px; color: #94a3b8;">{{ $cliente->telefone ?? '-' }}</td>
                        <td style="padding: 12px 8px; text-align: center; white-space: nowrap;">
                            <a href="/admin/clientes/editar/{{ $cliente->id_usuario }}" style="color: #06b6d4; text-decoration: none; margin-right: 12px; font-weight: 600;">Editar</a>
                            <a href="/admin/clientes/excluir/{{ $cliente->id_usuario }}" style="color: #ef4444; text-decoration: none; font-weight: 600;" onclick="return confirm('Deseja realmente excluir este cliente?')">Excluir</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="padding: 25px; text-align: center; color: #94a3b8;">
                            Nenhum cliente cadastrado ainda.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

<script>
    // Máscara de CPF
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

    // Máscara de Telefone
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
@endsection
