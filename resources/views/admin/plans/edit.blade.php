@extends('admin.layout')

@section('content')
<div class="content-header">
    <h1>Editar Plano</h1>
    <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary">Voltar</a>
</div>

<div class="content">
    <form action="{{ route('admin.plans.update', $plan) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Nome do Plano</label>
            <input type="text" name="name" id="name" class="form-control" required value="{{ old('name', $plan->name) }}">
        </div>

        <div class="form-group">
            <label for="price">Preço (AKZ)</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control" required value="{{ old('price', $plan->price) }}">
        </div>

        <div class="form-group">
            <label for="description">Descrição</label>
            <textarea name="description" id="description" class="form-control">{{ old('description', $plan->description) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success mt-2">Atualizar Plano</button>
    </form>
</div>
@endsection
