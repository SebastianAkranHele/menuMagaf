@extends('admin.layout')

@section('content')
<div class="container">
    <h2 class="mb-4">Página Inicial</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            Preview da Home
        </div>

        @php
            $defaultLinks = [
                ['name' => 'instagram', 'url' => 'https://instagram.com', 'target_blank' => true],
            ];

            $socialLinks = $hero->social_links ?: $defaultLinks;

            $socialOptions = [
                'facebook'  => ['label' => 'Facebook', 'icon' => 'fa-brands fa-facebook', 'color' => 'btn-primary'],
                'instagram' => ['label' => 'Instagram', 'icon' => 'fa-brands fa-instagram', 'color' => 'btn-danger'],
                'whatsapp'  => ['label' => 'WhatsApp', 'icon' => 'fa-brands fa-whatsapp', 'color' => 'btn-success'],
                'tiktok'    => ['label' => 'TikTok', 'icon' => 'fa-brands fa-tiktok', 'color' => 'btn-dark'],
            ];
        @endphp

        <div class="card-body text-center">
            <h3>{{ $hero->title ?? 'Título do Hero' }}</h3>
            <p>{{ $hero->subtitle ?? 'Subtítulo do Hero' }}</p>

            @if($hero->background_image)
                <img src="{{ asset('storage/'.$hero->background_image) }}"
                     alt="Background"
                     class="img-fluid mb-3"
                     style="max-height:200px;">
            @endif

            @if($hero->profile_image)
                <img src="{{ asset('storage/'.$hero->profile_image) }}"
                     alt="Perfil"
                     class="rounded-circle mb-3"
                     style="max-height:120px;">
            @endif

            <h5 class="mb-3">Acesse nosso menu digital</h5>

            <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
                <a href="{{ url('/menu') }}" class="btn btn-dark">
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
        </div>

        <!-- Footer -->
        <div class="card-footer text-center text-muted py-2">
            {{ $hero->footer_text ?? 'Magaf 2025 - Todos os direitos reservados' }}
        </div>
    </div>

    <!-- Botão de Edição -->
    <div class="text-center mt-4">
        <a href="{{ route('admin.home.hero.edit') }}" class="btn btn-danger btn-lg">
            <i class="fas fa-edit"></i> Editar Página Inicial
        </a>
    </div>
</div>
@endsection
