@extends('client.layout')

@section('content')
<div class="container">
    <h2 class="mb-4">Página Inicial</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card p-4 shadow">
        @php
            $defaultLinks = [
                ['name' => 'instagram', 'url' => 'https://instagram.com', 'target_blank' => true],
            ];

            $socialLinks = is_array($hero->social_links) && count($hero->social_links) > 0
                           ? $hero->social_links
                           : $defaultLinks;

            $socialOptions = [
                'facebook'  => ['label' => 'Facebook', 'icon' => 'fa-brands fa-facebook', 'color' => 'btn-primary'],
                'instagram' => ['label' => 'Instagram', 'icon' => 'fa-brands fa-instagram', 'color' => 'btn-danger'],
                'whatsapp'  => ['label' => 'WhatsApp', 'icon' => 'fa-brands fa-whatsapp', 'color' => 'btn-success'],
                'tiktok'    => ['label' => 'TikTok', 'icon' => 'fa-brands fa-tiktok', 'color' => 'btn-dark'],
            ];
        @endphp

        <div class="text-center">
            {{-- Imagem de background --}}
            @if($hero->background_image)
                <img src="{{ asset('storage/'.$hero->background_image) }}"
                     alt="Background"
                     class="img-fluid mb-3"
                     style="max-height:250px; width: 100%; object-fit: cover;">
            @endif

            {{-- Imagem de perfil --}}
            @if($hero->profile_image)
                <img src="{{ asset('storage/'.$hero->profile_image) }}"
                     alt="Perfil"
                     class="rounded-circle mb-3"
                     style="max-height:120px;">
            @endif

            <h3>{{ $hero->title ?? 'Título do Hero' }}</h3>
            <p>{{ $hero->subtitle ?? 'Subtítulo do Hero' }}</p>
        </div>

        {{-- Botões de ação --}}
        <div class="d-flex flex-column flex-sm-row justify-content-center gap-2 mt-3">
            <a href="{{ url('/'.$client->slug.'/menu') }}" class="btn btn-dark">
                <i class="fas fa-utensils"></i> Ver Menu Completo
            </a>

            @foreach($socialLinks as $link)
                @php
                    $key = strtolower($link['name'] ?? '');
                    $opt = $socialOptions[$key]
                           ?? ['icon' => 'fas fa-link', 'color' => 'btn-secondary', 'label' => ucfirst($key ?: 'Link')];
                @endphp
                <a href="{{ $link['url'] }}"
                   class="btn {{ $opt['color'] }}"
                   {{ !empty($link['target_blank']) ? 'target=_blank' : '' }}>
                    <i class="{{ $opt['icon'] }}"></i> {{ $opt['label'] }}
                </a>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="card-footer text-center text-muted py-2 mt-4">
            {{ $hero->footer_text ?? 'Magaf 2025 - Todos os direitos reservados' }}
        </div>
    </div>

    {{-- Botão de edição --}}
    <div class="text-center mt-4">
        <a href="{{ route('client.home.edit') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-edit"></i> Editar Página Inicial
        </a>
    </div>
</div>
@endsection
