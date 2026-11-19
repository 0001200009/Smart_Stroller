@extends('admin.index')

@section('conteudo')
<section id="produtos" class="section active">
    <div class="top">
        <h1>Gerenciar Produtos</h1>
    </div>
    <form class="box" method="POST" action="{{ isset($editarProduto) ? '/admin/produtos/salvar/'.$editarProduto->id_produto : '/admin/produtos/salvar' }}">
    
    @csrf 
    
    @if(isset($editarProduto))
        @method('PUT')
    @endif

    <h2>{{ isset($editarProduto) ? 'Editar Item' : 'Adicionar Novo Item' }}</h2>
    
    <input type="text" name="nome" id="nome" placeholder="Nome do Produto" value="{{ $editarProduto->nome ?? '' }}" required>
    
    <input type="number" name="preco" id="preco" placeholder="Preço (0.00)" step="0.01" value="{{ $editarProduto->preco ?? '' }}" required>

    <input type="text" name="codigo_rfid" id="codigo_rfid" placeholder="Código RFID" value="{{ $editarProduto->codigo_rfid ?? '' }}" required>
    
    <input type="number" name="estoque" id="estoque" placeholder="Estoque" value="{{ $editarProduto->estoque ?? '' }}" required>
    
    <select name="status" id="status">
        <option value="disponivel" {{ (isset($editarProduto) && $editarProduto->STATUS == 'disponivel') ? 'selected' : '' }}>Disponível</option>
        <option value="em promocao" {{ (isset($editarProduto) && $editarProduto->STATUS == 'em promocao') ? 'selected' : '' }}>Em Promoção</option>
        <option value="em falta" {{ (isset($editarProduto) && $editarProduto->STATUS == 'em falta') ? 'selected' : '' }}>Em falta</option>
    </select>
    
    <button type="submit" class="addBtn">{{ isset($editarProduto) ? 'Salvar Alterações' : 'Adicionar Produto' }}</button>
</form>
    <div class="box">
        <h2>Itens Atuais</h2>
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Preço</th>
                    <th>Estoque</th>
                    <th>Editar</th>
                    <th>Excluir</th>
                </tr>
            </thead>
            <tbody>
                @foreach($produtos as $produto)
                <tr>
                    <td>{{ $produto->nome }}</td>
                    <td>R$ {{ number_format($produto->preco, 2, ',', '.') }}</td>
                    <td>{{ $produto->estoque }}</td>
                    <td><a href="/admin/produtos/editar/{{ $produto->id_produto }}">Editar</a></td>
                    <td><a href="/admin/produtos/excluir/{{ $produto->id_produto }}" onclick="return confirm('Tem certeza que deseja excluir este produto?')">Excluir</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection