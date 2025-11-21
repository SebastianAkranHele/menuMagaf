@extends('admin.layout')

@section('content')
<div class="content-header">
    <h1>Novo Plano</h1>
    <a href="{{ route('admin.plans.index') }}" class="btn btn-secondary">Voltar</a>
</div>

<div class="content">
    <form action="{{ route('admin.plans.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Nome do Plano</label>
            <input type="text" name="name" id="name" class="form-control" required value="{{ old('name') }}">
        </div>

        <div class="form-group">
            <label for="price">Preço (AKZ)</label>
            <input type="number" step="0.01" name="price" id="price" class="form-control" required value="{{ old('price') }}">
        </div>

        <div class="form-group">
            <label for="description">Descrição</label>
            <textarea name="description" id="description" class="form-control">{{ old('description') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success mt-2">Salvar Plano</button>
    </form>
</div>
@endsection
