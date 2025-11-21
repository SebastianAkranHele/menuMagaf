@extends('admin.layout')

@section('content')
<h1>Clientes</h1>

<a href="{{ route('admin.clients.create') }}" class="btn btn-primary">Novo Cliente</a>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome do Negócio</th>
            <th>Email</th>
            <th>Plano</th>
            <th>Ativo</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        @foreach($clients as $client)
            <tr>
                <td>{{ $client->id }}</td>
                <td>{{ $client->business_name }}</td>
                <td>{{ $client->email }}</td>
                <td>{{ $client->plan->name ?? '-' }}</td>
                <td>{{ $client->active ? 'Sim' : 'Não' }}</td>
                <td>
                    <a href="{{ route('admin.clients.edit', $client->id) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('admin.clients.destroy', $client->id) }}" method="POST" style="display:inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Tem certeza que deseja remover este cliente?')">Excluir</button>
                    </form>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
