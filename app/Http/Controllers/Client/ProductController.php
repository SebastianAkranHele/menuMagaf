<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function index()
    {
        $products = auth()->user()->products()->with('category')->get();
        $categories = auth()->user()->categories; // apenas categorias do cliente
        return view('client.products.index', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:products,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only('name', 'description', 'price', 'category_id', 'stock');
        $data['client_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($data);

        return redirect()->route('client.products.index')->with('success', 'Produto criado com sucesso!');
    }

    public function update(Request $request, Product $product)
    {
        // Garante que o produto pertence ao cliente
        $product = auth()->user()->products()->findOrFail($product->id);

        $request->validate([
            'name' => 'required|string|max:255|unique:products,name,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $request->only('name', 'description', 'price', 'category_id', 'stock');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('client.products.index')->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy(Product $product)
    {
        $product = auth()->user()->products()->findOrFail($product->id);
        $product->delete();

        return redirect()->route('client.products.index')->with('success', 'Produto deletado com sucesso!');
    }
}
