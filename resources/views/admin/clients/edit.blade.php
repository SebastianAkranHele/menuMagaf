@extends('admin.layout')

@section('content')
<h1>Editar Cliente</h1>

<form action="{{ route('admin.clients.update', $client->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-group">
        <label>Nome do Negócio</label>
        <input type="text" name="business_name" class="form-control" value="{{ old('business_name', $client->business_name) }}" required>
    </div>

    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $client->email) }}" required>
    </div>

    <div class="form-group">
        <label>Nova Senha (opcional)</label>
        <input type="password" name="password" class="form-control">
    </div>

    <div class="form-group">
        <label>Confirmar Senha</label>
        <input type="password" name="password_confirmation" class="form-control">
    </div>

    <div class="form-group">
        <label>Plano</label>
        <select name="plan_id" class="form-control">
            <option value="">-- Nenhum --</option>
            @foreach($plans as $plan)
                <option value="{{ $plan->id }}" {{ $client->plan_id == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label>Ativo</label>
        <select name="active" class="form-control">
            <option value="1" {{ $client->active ? 'selected' : '' }}>Sim</option>
            <option value="0" {{ !$client->active ? 'selected' : '' }}>Não</option>
        </select>
    </div>

    <button type="submit" class="btn btn-success">Atualizar Cliente</button>
    <a href="{{ route('admin.clients.index') }}" class="btn btn-secondary">Cancelar</a>
</form>
@endsection
