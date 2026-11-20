@extends('admin.index')

@section('conteudo')
<section id="compras" class="section active">
    <div class="top" style="margin-bottom: 25px;">
        <h1 style="font-size: 26px; font-weight: 700;">Histórico Geral de Compras & Transações</h1>
        <p style="color: #94a3b8; font-size: 14px; margin-top: 4px;">Auditoria de pedidos realizados nos carrinhos, emissão de comprovantes e conferência de caixa express</p>
    </div>

    <!-- Formulário de Nova Compra ou Edição -->
    <form class="box" method="POST" action="{{ isset($editarCompra) ? '/admin/compras/salvar/'.$editarCompra->id_compra : '/admin/compras/salvar' }}">
        @csrf
        @if(isset($editarCompra))
            @method('PUT')
        @endif

        <h2 style="display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#06b6d4" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="9" cy="21" r="1"></circle>
                <circle cx="20" cy="21" r="1"></circle>
                <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
            </svg>
            {{ isset($editarCompra) ? 'Editar Compra #ST-'.$editarCompra->id_compra : 'Registrar Nova Compra' }}
        </h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 15px;">
            <div>
                <label style="display: block; font-size: 13px; color: #94a3b8; margin-bottom: 5px;">Cliente Comprador *</label>
                <select name="id_usuario" id="id_usuario" style="width: 100%; padding: 12px; border-radius: 8px; background: #0f172a; color: white; border: 1px solid #334155; outline: none;" required>
                    <option value="">Selecione um cliente...</option>
                    @foreach($usuarios as $usr)
                        <option value="{{ $usr->id_usuario }}" {{ (isset($editarCompra) && $editarCompra->id_usuario == $usr->id_usuario) ? 'selected' : '' }}>
                            {{ $usr->nome }} ({{ $usr->cpf ?? 'Sem CPF' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 13px; color: #94a3b8; margin-bottom: 5px;">Carrinho (Smart Stroller) *</label>
                <select name="id_stroller" id="id_stroller" style="width: 100%; padding: 12px; border-radius: 8px; background: #0f172a; color: white; border: 1px solid #334155; outline: none;" required>
                    @foreach($strollers as $st)
                        <option value="{{ $st->id_stroller }}" {{ (isset($editarCompra) && $editarCompra->id_stroller == $st->id_stroller) ? 'selected' : '' }}>
                            {{ $st->numero_identificacao }} (Bateria: {{ $st->nivel_bateria }}%)
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; font-size: 13px; color: #94a3b8; margin-bottom: 5px;">Valor Total (R$) *</label>
                <input type="number" name="valor_total" id="valor_total" placeholder="0.00" step="0.01" value="{{ $editarCompra->valor_total ?? '0.00' }}" required>
            </div>

            <div>
                <label style="display: block; font-size: 13px; color: #94a3b8; margin-bottom: 5px;">Status da Transação</label>
                <select name="status" id="status" style="width: 100%; padding: 12px; border-radius: 8px; background: #0f172a; color: white; border: 1px solid #334155; outline: none;">
                    <option value="Em Andamento" {{ (isset($editarCompra) && $editarCompra->status == 'Em Andamento') ? 'selected' : '' }}>Em Andamento (No Carrinho)</option>
                    <option value="Pagamento" {{ (isset($editarCompra) && $editarCompra->status == 'Pagamento') ? 'selected' : '' }}>Pagamento (No Caixa Express)</option>
                    <option value="Concluida" {{ (isset($editarCompra) && $editarCompra->status == 'Concluida') ? 'selected' : '' }}>Concluída (Pago & Emitido)</option>
                    <option value="Cancelada" {{ (isset($editarCompra) && $editarCompra->status == 'Cancelada') ? 'selected' : '' }}>Cancelada (Abandono)</option>
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
                {{ isset($editarCompra) ? 'Salvar Alterações' : 'Adicionar Compra' }}
            </button>
            @if(isset($editarCompra))
                <a href="/admin/compras" class="btn btn-secondary" style="padding: 12px 20px;">Cancelar</a>
            @endif
        </div>
    </form>

    <!-- Tabela de Compras -->
    <div class="box">
        <h2 style="display: flex; align-items: center; justify-content: space-between;">
            <span>Auditoria de Compras Realizadas ({{ $compras->count() }})</span>
        </h2>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 14px;">
                <thead>
                    <tr style="border-bottom: 2px solid #334155; color: #94a3b8;">
                        <th style="padding: 12px 8px;">Código Pedido</th>
                        <th style="padding: 12px 8px;">Cliente</th>
                        <th style="padding: 12px 8px;">Carrinho</th>
                        <th style="padding: 12px 8px;">Valor Total</th>
                        <th style="padding: 12px 8px;">Status</th>
                        <th style="padding: 12px 8px;">Data & Hora</th>
                        <th style="padding: 12px 8px; text-align: center;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compras as $compra)
                    <tr style="border-bottom: 1px solid #1e293b;">
                        <td style="padding: 12px 8px; font-weight: 700; color: #06b6d4; font-family: monospace;">#ST-{{ str_pad($compra->id_compra, 4, '0', STR_PAD_LEFT) }}</td>
                        <td style="padding: 12px 8px; font-weight: 600; color: #ffffff;">{{ $compra->usuario->nome ?? 'Cliente #'.$compra->id_usuario }}</td>
                        <td style="padding: 12px 8px; color: #94a3b8;">{{ $compra->stroller->numero_identificacao ?? 'Stroller #'.$compra->id_stroller }}</td>
                        <td style="padding: 12px 8px; font-weight: bold; color: #10b981;">R$ {{ number_format($compra->valor_total, 2, ',', '.') }}</td>
                        <td style="padding: 12px 8px;">
                            @if($compra->status === 'Concluida')
                                <span style="background: rgba(16, 185, 129, 0.15); color: #10b981; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                    Concluída
                                </span>
                            @elseif($compra->status === 'Em Andamento')
                                <span style="background: rgba(6, 182, 212, 0.15); color: #06b6d4; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                    Em Andamento
                                </span>
                            @elseif($compra->status === 'Pagamento')
                                <span style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                    No Caixa
                                </span>
                            @else
                                <span style="background: rgba(239, 68, 68, 0.15); color: #ef4444; padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600;">
                                    Cancelada
                                </span>
                            @endif
                        </td>
                        <td style="padding: 12px 8px; color: #94a3b8; font-size: 13px;">
                            {{ \Carbon\Carbon::parse($compra->data_criacao)->format('d/m/Y H:i') }}
                        </td>
                        <td style="padding: 12px 8px; text-align: center; white-space: nowrap;">
                            <a href="/recibo/imprimir/{{ $compra->id_compra }}" target="_blank" style="color: #10b981; text-decoration: none; margin-right: 10px; font-weight: 600;">Recibo</a>
                            <a href="/admin/compras/editar/{{ $compra->id_compra }}" style="color: #06b6d4; text-decoration: none; margin-right: 10px; font-weight: 600;">Editar</a>
                            <a href="/admin/compras/excluir/{{ $compra->id_compra }}" style="color: #ef4444; text-decoration: none; font-weight: 600;" onclick="return confirm('Deseja realmente excluir esta compra?')">Excluir</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="padding: 25px; text-align: center; color: #94a3b8;">
                            Nenhuma transação registrada no histórico.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
