<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // Lista todas as categorias do cliente
    public function index()
    {
        $categories = auth()->user()->categories;
        return view('client.categories.index', compact('categories'));
    }

    // Cria nova categoria
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|unique:categories,name,NULL,id,client_id,' . auth()->id()
        ]);

        auth()->user()->categories()->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('client.categories.index')
                         ->with('success', 'Categoria criada com sucesso!');
    }

    // Atualiza categoria
    public function update(Request $request, Category $category)
    {
        // Garante que pertence ao cliente
        $category = auth()->user()->categories()->findOrFail($category->id);

        $request->validate([
            'name' => 'required|max:255|unique:categories,name,' . $category->id . ',id,client_id,' . auth()->id()
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return redirect()->route('client.categories.index')
                         ->with('success', 'Categoria atualizada com sucesso!');
    }

    // Deleta categoria (verifica produtos do cliente)
    public function destroy(Category $category)
    {
        $category = auth()->user()->categories()->findOrFail($category->id);

        if ($category->products()->count() > 0) {
            return redirect()->route('client.categories.index')
                             ->with('error', 'Não é possível deletar uma categoria que possui produtos.');
        }

        $category->delete();

        return redirect()->route('client.categories.index')
                         ->with('success', 'Categoria deletada com sucesso!');
    }
}
