<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Plan;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::with('plan')->get();
        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        $plans = Plan::all();
        return view('admin.clients.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'business_name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'password' => 'required|string|min:6|confirmed',
            'plan_id' => 'nullable|exists:plans,id',
            'active' => 'boolean',
        ]);

        $data['password'] = Hash::make($data['password']);

        Client::create($data);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente criado com sucesso!');
    }

    public function edit(Client $client)
    {
        $plans = Plan::all();
        return view('admin.clients.edit', compact('client', 'plans'));
    }

    public function update(Request $request, Client $client)
    {
        $data = $request->validate([
            'business_name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,' . $client->id,
            'password' => 'nullable|string|min:6|confirmed',
            'plan_id' => 'nullable|exists:plans,id',
            'active' => 'boolean',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $client->update($data);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Client $client)
    {
        $client->delete();
        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente removido com sucesso!');
    }
}
