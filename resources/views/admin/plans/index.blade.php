@extends('admin.layout')

@section('content')
<div class="content-header">
    <h1>Planos</h1>
    <a href="{{ route('admin.plans.create') }}" class="btn btn-primary">Novo Plano</a>
</div>

<div class="content">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Preço</th>
                <th>Descrição</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($plans as $plan)
                <tr>
                    <td>{{ $plan->id }}</td>
                    <td>{{ $plan->name }}</td>
                    <td>{{ number_format($plan->price, 2, ',', '.') }} AKZ</td>
                    <td>{{ $plan->description }}</td>
                    <td>
                        <a href="{{ route('admin.plans.edit', $plan) }}" class="btn btn-sm btn-warning">Editar</a>
                        <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Tem certeza que deseja excluir este plano?')">Excluir</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Nenhum plano cadastrado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
