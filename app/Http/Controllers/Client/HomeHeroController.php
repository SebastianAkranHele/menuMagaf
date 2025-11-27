<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\HomeHero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeHeroController extends Controller
{
    /**
     * Exibe a home no dashboard do cliente.
     */
    public function index()
    {
        $client = auth('client')->user();
        $hero = $this->getHero($client);

        return view('client.home.index', compact('hero', 'client'));
    }

    /**
     * Tela de edição da home.
     */
    public function edit()
    {
        $client = auth('client')->user();
        $hero = $this->getHero($client);

        return view('client.home.edit', compact('hero', 'client'));
    }

    /**
     * Atualiza o hero do cliente logado.
     */
    public function update(Request $request)
    {
        $request->validate([
            'title'                     => 'required|string|max:255',
            'subtitle'                  => 'required|string|max:255',
            'background_image'          => 'nullable|image|max:2048',
            'background_image_url'      => 'nullable|url|max:2048',
            'background_image_type'     => 'nullable|in:file,url',
            'remove_background_image'   => 'nullable|boolean',
            'profile_image'             => 'nullable|image|max:2048',
            'profile_image_url'         => 'nullable|url|max:2048',
            'profile_image_type'        => 'nullable|in:file,url',
            'remove_profile_image'      => 'nullable|boolean',
            'social_links'              => 'nullable|array',
            'social_links.*.name'       => 'nullable|string|max:255',
            'social_links.*.url'        => 'nullable|string|max:255',
            'social_links.*.icon_class' => 'nullable|string|max:255',
            'social_links.*.color_class'=> 'nullable|string|max:255',
            'social_links.*.target_blank'=> 'nullable',
            'profile_title'             => 'nullable|string|max:255',
            'profile_subtitle'          => 'nullable|string|max:255',
        ]);

        $client = auth('client')->user();
        $hero = $this->getHero($client);

        // Atualiza campos simples
        $hero->title = $request->title;
        $hero->subtitle = $request->subtitle;
        $hero->profile_title = $request->profile_title;
        $hero->profile_subtitle = $request->profile_subtitle;

        // Atualiza imagens
        $hero->background_image = $this->handleImageUpload(
            $request,
            'background_image',
            $request->background_image_url,
            $request->background_image_type,
            $hero->background_image,
            $request->remove_background_image
        );

        $hero->profile_image = $this->handleImageUpload(
            $request,
            'profile_image',
            $request->profile_image_url,
            $request->profile_image_type,
            $hero->profile_image,
            $request->remove_profile_image
        );

        // Atualiza links sociais
        $hero->social_links = $this->sanitizeSocialLinks($request->social_links ?? []);

        $hero->client_id = $client->id;
        $hero->save();

        return redirect()
            ->route('client.home.edit')
            ->with('success', 'Página inicial atualizada com sucesso!');
    }

    /**
     * Página pública da home do cliente.
     */
    public function publicHome($clientSlug = null)
    {
        if ($clientSlug) {
            $client = \App\Models\Client::where('slug', $clientSlug)->firstOrFail();
        } else {
            $client = auth('client')->user();
            if (!$client) {
                abort(404, 'Cliente não encontrado.');
            }
        }

        $hero = $this->getHero($client);

        $categories = $client->categories()->with('products')->get();
        $products = $client->products()->where('stock', '>', 0)->get();

        return view('client.home.public', compact('client', 'hero', 'categories', 'products'));
    }

    /**
     * Pega ou cria o registro do hero do cliente.
     */
    protected function getHero($client)
    {
        $hero = HomeHero::firstOrCreate(
            ['client_id' => $client->id],
            [
                'title' => 'Bem-vindo(a) ao menu do cliente',
                'subtitle' => 'Este é um subtítulo padrão',
                'profile_title' => 'Perfil do Cliente',
                'profile_subtitle' => 'Subtítulo do perfil',
                'background_image' => null,
                'profile_image' => null,
                'social_links' => [],
                'footer_text' => 'Todos os direitos reservados',
            ]
        );

        return $hero;
    }

    /**
     * Trata upload de imagem ou uso de link externo.
     */
    private function handleImageUpload($request, $fieldName, $url, $type, $oldValue, $remove)
    {
        $client = auth('client')->user();
        $clientFolder = "hero/client_{$client->id}";

        if ($remove) {
            if ($oldValue && Storage::exists("public/" . $oldValue)) {
                Storage::delete("public/" . $oldValue);
            }
            return null;
        }

        if ($type === 'file' && $request->hasFile($fieldName)) {
            if ($oldValue && Storage::exists("public/" . $oldValue)) {
                Storage::delete("public/" . $oldValue);
            }

            $file = $request->file($fieldName);
            $filename = time() . '_' . $file->getClientOriginalName();
            return $file->storeAs($clientFolder, $filename, 'public');
        }

        if ($type === 'url' && $url) {
            return $url;
        }

        return $oldValue;
    }

    /**
     * Sanitiza links sociais.
     */
    private function sanitizeSocialLinks($links)
    {
        return collect($links)
            ->filter(fn($l) => !empty($l['name']) && !empty($l['url']))
            ->values()
            ->toArray();
    }
}
