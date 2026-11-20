@extends('admin.index')

@section('conteudo')
<section id="produtos" class="section active">
    <div class="top" style="margin-bottom: 25px;">
        <h1 style="font-size: 26px; font-weight: 700;">Gerenciar Produtos & Tags RFID</h1>
        <p style="color: #94a3b8; font-size: 14px; margin-top: 4px;">Cadastro e manutenção do catálogo com tags RFID UHF e códigos de barra para o checkout express</p>
    </div>

    <!-- Formulário de Adicionar / Editar -->
    <form class="box" method="POST" action="{{ isset($editarProduto) ? '/admin/produtos/salvar/'.$editarProduto->id_produto : '/admin/produtos/salvar' }}">
        @csrf
        @if(isset($editarProduto))
            @method('PUT')
        @endif

        <h2 style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
            </svg>
            {{ isset($editarProduto) ? 'Editar Produto: '.$editarProduto->nome : 'Cadastrar Novo Produto' }}
        </h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">
            <div>
                <label style="display: block; font-size: 13px; color: #94a3b8; margin-bottom: 5px;">Nome do Produto *</label>
                <input type="text" name="nome" id="nome" placeholder="Ex: Leite Integral 1L" value="{{ $editarProduto->nome ?? old('nome') }}" required>
            </div>

            <div>
                <label style="display: block; font-size: 13px; color: #94a3b8; margin-bottom: 5px;">Preço Unitário (R$) *</label>
                <input type="number" name="preco" id="preco" placeholder="0.00" step="0.01" value="{{ $editarProduto->preco ?? old('preco') }}" required>
            </div>

            <div>
                <label style="display: block; font-size: 13px; color: #94a3b8; margin-bottom: 5px;">Código RFID (Hex) *</label>
                <input type="text" name="codigo_rfid" id="codigo_rfid" placeholder="Ex: 337057F5" value="{{ $editarProduto->codigo_rfid ?? old('codigo_rfid') }}" required>
            </div>

            <div>
                <label style="display: block; font-size: 13px; color: #94a3b8; margin-bottom: 5px;">Código de Barras (EAN-13)</label>
                <input type="text" name="codigo_barras" id="codigo_barras" placeholder="Ex: 789000001" value="{{ $editarProduto->codigo_barras ?? old('codigo_barras') }}">
            </div>

            <div>
                <label style="display: block; font-size: 13px; color: #94a3b8; margin-bottom: 5px;">Categoria</label>
                <select name="categoria" id="categoria" style="width: 100%; padding: 12px; border-radius: 8px; background: #0f172a; color: white; border: 1px solid #334155; outline: none;">
                    <option value="">Selecione uma categoria...</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id_categoria }}" {{ (isset($editarProduto) && $editarProduto->id_categoria == $cat->id_categoria) ? 'selected' : '' }}>
                            {{ $cat->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 13px; color: #94a3b8; margin-bottom: 5px;">Quantidade em Estoque</label>
                <input type="number" name="estoque" id="estoque" placeholder="Ex: 100" value="{{ $editarProduto->estoque ?? old('estoque', 50) }}" required>
            </div>

            <div>
                <label style="display: block; font-size: 13px; color: #94a3b8; margin-bottom: 5px;">Status do Produto</label>
                <select name="status" id="status" style="width: 100%; padding: 12px; border-radius: 8px; background: #0f172a; color: white; border: 1px solid #334155; outline: none;">
                    <option value="disponivel" {{ (isset($editarProduto) && strtolower($editarProduto->status) == 'disponivel') ? 'selected' : '' }}>Disponível</option>
                    <option value="em promocao" {{ (isset($editarProduto) && strtolower($editarProduto->status) == 'em promocao') ? 'selected' : '' }}>Em Promoção (Aparece no Carrinho)</option>
                    <option value="em falta" {{ (isset($editarProduto) && strtolower($editarProduto->status) == 'em falta') ? 'selected' : '' }}>Em Falta (Gera Alerta)</option>
                </select>
            </div>
        </div>

        <div style="display: flex; gap: 10px;">
            <button type="submit" class="addBtn" style="flex: 1;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                    <polyline points="17 21 17 13 7 13 7 21"></polyline>
                    <polyline points="7 3 7 8 15 8"></polyline>
                </svg>
                {{ isset($editarProduto) ? 'Salvar Alterações' : 'Cadastrar Produto' }}
            </button>
            @if(isset($editarProduto))
                <a href="/admin/produtos" class="btn btn-secondary" style="padding: 12px 20px;">Cancelar</a>
            @endif
        </div>
    </form>

    <!-- Tabela de Produtos Cadastrados -->
    <div class="box">
        <h2 style="display: flex; align-items: center; justify-content: space-between;">
            <span>Produtos Cadastrados ({{ $produtos->count() }})</span>
        </h2>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                <thead>
                    <tr style="border-bottom: 2px solid #334155; color: #94a3b8;">
                        <th style="padding: 12px 8px;">Produto</th>
                        <th style="padding: 12px 8px;">Categoria</th>
                        <th style="padding: 12px 8px;">Preço</th>
                        <th style="padding: 12px 8px;">Tag RFID</th>
                        <th style="padding: 12px 8px;">Cód. Barras</th>
                        <th style="padding: 12px 8px;">Estoque</th>
                        <th style="padding: 12px 8px;">Status</th>
                        <th style="padding: 12px 8px; text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($produtos as $produto)
                    <tr style="border-bottom: 1px solid #1e293b;">
                        <td style="padding: 12px 8px; font-weight: 600; color: #ffffff;">{{ $produto->nome }}</td>
                        <td style="padding: 12px 8px; color: #94a3b8;">{{ $produto->categoria->nome ?? '-' }}</td>
                        <td style="padding: 12px 8px; font-weight: bold; color: #10b981;">R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                        <td style="padding: 12px 8px; font-family: monospace; color: #06b6d4;">{{ $produto->codigo_rfid }}</td>
                        <td style="padding: 12px 8px; font-family: monospace; color: #94a3b8;">{{ $produto->codigo_barras ?? '-' }}</td>
                        <td style="padding: 12px 8px;">
                            <span style="color: {{ $produto->estoque < 20 ? '#ef4444' : '#ffffff' }}; font-weight: 600;">
                                {{ $produto->estoque }} un
                            </span>
                        </td>
                        <td style="padding: 12px 8px;">
                            @if($produto->status === 'em promocao')
                                <span style="background: rgba(6, 182, 212, 0.15); color: #06b6d4; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                    Em Promoção
                                </span>
                            @elseif($produto->status === 'em falta')
                                <span style="background: rgba(239, 68, 68, 0.15); color: #ef4444; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                    Em Falta
                                </span>
                            @else
                                <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                    Disponível
                                </span>
                            @endif
                        </td>
                        <td style="padding: 12px 8px; text-align: center; white-space: nowrap;">
                            <a href="/admin/produtos/editar/{{ $produto->id_produto }}" style="color: #06b6d4; text-decoration: none; margin-right: 12px; font-weight: 600;">Editar</a>
                            <a href="/admin/produtos/excluir/{{ $produto->id_produto }}" style="color: #ef4444; text-decoration: none; font-weight: 600;" onclick="return confirm('Deseja realmente excluir este produto?')">Excluir</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="padding: 25px; text-align: center; color: #94a3b8;">
                            Nenhum produto cadastrado no catálogo.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
