<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::all();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_products' => 'nullable|integer|min:0',
            'max_categories' => 'nullable|integer|min:0',
            'custom_domain' => 'boolean',
        ]);

        Plan::create($data);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plano criado com sucesso!');
    }

    public function edit(Plan $plan)
    {
        return view('admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'max_products' => 'nullable|integer|min:0',
            'max_categories' => 'nullable|integer|min:0',
            'custom_domain' => 'boolean',
        ]);

        $plan->update($data);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plano atualizado com sucesso!');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();
        return redirect()->route('admin.plans.index')
            ->with('success', 'Plano removido com sucesso!');
    }
}
