<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // Lista todas as categorias
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    // Salva nova categoria (do modal)
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name|max:255'
        ]);

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Categoria criada com sucesso!');
    }

    // Atualiza categoria existente (do modal)
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id . '|max:255'
        ]);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Categoria atualizada com sucesso!');
    }

    // Deleta categoria (verificando vinculação a produtos)
    public function destroy(Category $category)
    {
        if ($category->products()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Não é possível deletar uma categoria que possui produtos vinculados.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Categoria deletada com sucesso!');
    }
}
