<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomeHero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomeHeroController extends Controller
{
    /**
     * Retorna um registro do Hero ou defaults.
     */
    private function getHero(): HomeHero
    {
        return HomeHero::firstOrNew([], $this->getDefaultHero());
    }

    /**
     * Valores padrão para o Hero.
     */
    private function getDefaultHero(): array
    {
        return [
            'title'            => 'Título do Hero',
            'subtitle'         => 'Subtítulo do Hero',
            'background_image' => null,
            'social_links'     => [],
            'profile_image'    => null,
            'profile_title'    => 'Experimente o sabor autêntico',
            'profile_subtitle' => 'O ponto de referência',
        ];
    }

    /**
     * Exibe a home no dashboard.
     */
    public function index()
    {
        $hero = $this->getHero();
        return view('admin.home.index', compact('hero'));
    }

    /**
     * Exibe formulário de edição.
     */
    public function edit()
    {
        $hero = $this->getHero();
        return view('admin.home.hero_edit', compact('hero'));
    }

    /**
     * Atualiza o Hero.
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

        $hero = HomeHero::firstOrNew();

        // Campos básicos
        $hero->title = $request->title;
        $hero->subtitle = $request->subtitle;
        $hero->profile_title = $request->profile_title;
        $hero->profile_subtitle = $request->profile_subtitle;

        // Uploads ou URLs
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

        // Links sociais
        $links = $this->sanitizeSocialLinks($request->social_links ?? []);
        $hero->social_links = $links;

        $hero->save();

        return redirect()
            ->route('admin.home.index')
            ->with('success', 'Página inicial atualizada com sucesso!');
    }

    /**
     * Exibe a home pública.
     */
    public function publicHome()
    {
        $hero = $this->getHero();
        return view('home', compact('hero'));
    }

    /**
     * Upload ou URL de imagem genérica.
     */
    private function handleImageUpload(Request $request, string $fileField, ?string $urlField, ?string $type, ?string $currentImage, ?bool $remove = false): ?string
    {
        // Remover imagem atual
        if ($remove && $currentImage) {
            if (Storage::disk('public')->exists($currentImage)) {
                Storage::disk('public')->delete($currentImage);
            }
            return null;
        }

        // Upload local
        if ($type === 'file' && $request->hasFile($fileField)) {
            if ($currentImage && Storage::disk('public')->exists($currentImage)) {
                Storage::disk('public')->delete($currentImage);
            }
            return $request->file($fileField)->store('home_hero', 'public');
        }

        // URL externa
        if ($type === 'url' && $urlField) {
            if ($currentImage && Storage::disk('public')->exists($currentImage)) {
                Storage::disk('public')->delete($currentImage);
            }
            return $urlField;
        }

        return $currentImage;
    }

    /**
     * Normaliza e valida links sociais.
     */
    private function sanitizeSocialLinks(array $links): array
    {
        $allowed = [
            'facebook'  => ['icon' => 'fa-brands fa-facebook', 'color' => 'facebook-color'],
            'instagram' => ['icon' => 'fa-brands fa-instagram', 'color' => 'instagram-color'],
            'whatsapp'  => ['icon' => 'fa-brands fa-whatsapp', 'color' => 'whatsapp-color'],
            'tiktok'    => ['icon' => 'fa-brands fa-tiktok', 'color' => 'tiktok-color'],
        ];

        $clean = [];

        foreach ($links as $raw) {
            $name = strtolower(trim($raw['name'] ?? ''));
            $url  = trim($raw['url'] ?? '');
            $target = !empty($raw['target_blank']);

            if ($url === '') continue;

            if (str_starts_with($url, 'wa.me')) {
                $url = 'https://' . ltrim($url, '/');
            }
            if (!preg_match('/^https?:\/\//', $url)) {
                $url = 'https://' . $url;
            }

            $icon = $allowed[$name]['icon'] ?? ($raw['icon_class'] ?? 'fa-solid fa-link');
            $color = $allowed[$name]['color'] ?? ($raw['color_class'] ?? 'social-default');

            $clean[] = [
                'name'        => $name ?: ($raw['name'] ?? ''),
                'url'         => $url,
                'icon_class'  => $icon,
                'color_class' => $color,
                'target_blank'=> (bool) $target,
            ];
        }

        return $clean;
    }
}
