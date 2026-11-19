@extends('admin.index')

@section('conteudo')
    <h1 class="section active">Histórico Geral de Compras</h1>

    <div class="box">
        <div class="box">
            <input type="text" class="search-input" placeholder="Buscar por número do pedido...">
            <button type="button" class="addBtn"><i class="fa-solid fa-filter"></i> Buscar</button>
        </div>
    </div>

    <form class="box" method="POST" action="{{ isset($editarCompra) ? '/admin/compras/salvar/'.$editarCompra->id_compra : '/admin/compras/salvar' }}">
    
        @csrf 
    
        @if(isset($editarCompra))
            @method('PUT')
        @endif

        <h2>{{ isset($editarCompra) ? 'Editar Compra #'.$editarCompra->id_compra : 'Adicionar Nova Compra' }}</h2>
    
        <input type="number" name="id_usuario" id="id_usuario" placeholder="ID do Comprador" step="1" value="{{ $editarCompra->id_usuario ?? '' }}" required>

        <input type="number" name="id_stroller" id="id_stroller" placeholder="ID do Stroller" step="1" value="{{ $editarCompra->id_stroller ?? '1' }}" required>
    
        <input type="number" name="valor_total" id="valor_total" placeholder="Valor Total (R$)" step="0.01" value="{{ $editarCompra->valor_total ?? '0.00' }}" required>
    
        <select name="status" id="status">
            <option value="Em Andamento" {{ (isset($editarCompra) && $editarCompra->status == 'Em Andamento') ? 'selected' : '' }}>Em Andamento</option>
            <option value="Pagamento" {{ (isset($editarCompra) && $editarCompra->status == 'Pagamento') ? 'selected' : '' }}>Pagamento</option>
            <option value="Concluida" {{ (isset($editarCompra) && $editarCompra->status == 'Concluida') ? 'selected' : '' }}>Concluída</option>
        </select>
    
        <button type="submit" class="addBtn">{{ isset($editarCompra) ? 'Salvar Alterações' : 'Adicionar Compra' }}</button>
    </form>

    <div class="box">
        <div class="box">
            <table class="box">
                <thead>
                    <tr>
                        <th>ID da Compra</th>
                        <th>ID do Comprador</th>
                        <th>Valor Total</th>
                        <th>Status</th>
                        <th>Editar</th>
                        <th>Excluir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($compras as $compra)
                    <tr>
                        <td>#ST-{{ $compra->id_compra }}</td>
                        <td>{{ $compra->id_usuario }}</td>
                        <td><strong>R$ {{ number_format($compra->valor_total, 2, ',', '.') }}</strong></td>
                        <td>{{ $compra->status }}</td>
                        <td><a href="{{ '/admin/compras/editar/'.$compra->id_compra }}" class="btn btn-primary">Editar</a></td>
                        <td><a href="{{ '/admin/compras/excluir/'.$compra->id_compra }}" class="btn btn-danger">Excluir</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection