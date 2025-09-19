<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        $categories = Category::all();
        return view('admin.products.index', compact('products', 'categories'));
    }

public function store(Request $request)
{
    $request->validate([
        'name' => 'required|max:255',
        'price' => 'required|numeric',
        'category_id' => 'required|exists:categories,id',
        'image' => 'nullable|image|max:2048'
    ]);

    // Verifica duplicado só se NÃO for confirmação
    if (!$request->has('force_create')) {
        $exists = Product::where('name', $request->name)->exists();

        if ($exists) {
           return redirect()
                ->route('admin.products.index')
                ->withInput()
                ->with('duplicate_create', $request->name);
        }
    }

    $data = $request->only('name', 'description', 'price', 'category_id');

    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('products', 'public');
    }

    Product::create($data);

    return redirect()->route('admin.products.index')->with('success', 'Produto criado com sucesso!');
}


public function update(Request $request, Product $product)
{
    $request->validate([
        'name' => 'required|max:255',
        'price' => 'required|numeric',
        'category_id' => 'required|exists:categories,id',
        'image' => 'nullable|image|max:2048'
    ]);

    // Verifica duplicado só se NÃO for confirmação
    if (!$request->has('force_update')) {
        $exists = Product::where('name', $request->name)
                        ->where('id', '!=', $product->id)
                        ->exists();

        if ($exists) {
            return redirect()
                ->route('admin.products.index')
                ->withInput()
                ->with('duplicate_update', $request->name)
                ->with('product_id', $product->id);
        }
    }

    $data = $request->only('name', 'description', 'price', 'category_id');

    if ($request->hasFile('image')) {
        $data['image'] = $request->file('image')->store('products', 'public');
    }

    $product->update($data);

    return redirect()->route('admin.products.index')->with('success', 'Produto atualizado com sucesso!');
}



    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produto deletado com sucesso!');
    }
}
