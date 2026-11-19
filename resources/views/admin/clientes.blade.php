@extends('admin.index')

@section('conteudo')
    <h1 class="section active">Gestão de Clientes</h1>

    <div class="box">
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Buscar cliente por nome ou CPF...">
            <button type="button" class="addBtn"><i class="fa-solid fa-magnifying-glass"></i> Filtrar</button>
        </div>
        <p style="color: #777; font-size: 14px;">Mostrando dados estruturados prontos para Eloquent</p>
    </div>
    <form class="box" method="POST" action="{{ isset($editarCliente) ? '/admin/clientes/salvar/'.$editarCliente->id_usuario : '/admin/clientes/salvar' }}">
    
        @csrf 
    
    @if(isset($editarCliente))
        @method('PUT')
    @endif

    <h2>{{ isset($editarCliente) ? 'Editar Cliente' : 'Adicionar Novo Cliente' }}</h2>
    
    <input type="text" name="nome" id="nome" placeholder="Nome do Cliente" value="{{ $editarCliente->nome ?? '' }}" required>
    
    <input type="email" name="email" id="email" placeholder="E-mail do Cliente" value="{{ $editarCliente->email ?? '' }}" required>
    
    <input type="number" name="telefone" id="telefone" placeholder="Número de Telefone" value="{{ $editarCliente->telefone ?? '' }}" required>
    
    
    <button type="submit" class="addBtn">{{ isset($editarCliente) ? 'Salvar Alterações' : 'Adicionar Cliente' }}</button>
</form>

    <div class="box">
        <div class="box">
            <table class="box">
                <thead>
                    <tr>
                        <th>Nome do Cliente</th>
                        <th>E-mail</th>
                        <th>ID do Cliente</th>
                        <th>Editar</th>
                        <th>Excluir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($clientes as $cliente)
                    <tr>
                        <td><strong>{{ $cliente->nome }}</strong></td>
                        <td>{{ $cliente->email }}</td>
                        <td>{{ $cliente->id_usuario }}</td>
                        <td><a href="{{ '/admin/clientes/editar/'.$cliente->id_usuario }}">Editar</a></td>
                        <td><a href="{{ '/admin/clientes/excluir/'.$cliente->id_usuario }}">Excluir</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection