<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $hero->title ?? 'MAGAF - Menu Digital' }}</title>
    @vite(['resources/css/home.css', 'resources/js/home.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Header / Logo -->
        <header>
            <div class="logo">
                <h1>{{ $hero->title ?? 'Garrafeira das 5 curvas' }}</h1>
                <p>{{ $hero->subtitle ?? 'MENU DIGITAL' }}</p>
            </div>
        </header>

        <main>
            <!-- Hero -->
                <section class="hero" style="{{ $hero->background_image ? 'background: linear-gradient(rgba(58,45,40,0.8), rgba(58,45,40,0.8)), url('.asset('storage/'.$hero->background_image).') no-repeat center center/cover;' : '' }}">
                    
                </section>


            <!-- Profile -->
            <section class="profile">
                <img src="{{ $hero->profile_image ? asset('storage/'.$hero->profile_image) : asset('assets/qrcode-magavi (4).png') }}"
                     alt="Perfil" class="profile-img">
                <div class="profile-content">
                    <h2>{{ $hero->profile_title ?? 'Experimente o sabor autêntico' }}</h2>
                    <p>{{ $hero->profile_subtitle ?? 'O ponto de referência' }}</p>
                </div>
            </section>

            <!-- Social Links -->
            <section class="social-links">
                <h3>Acesse nosso menu digital</h3>
                <div class="links-container">
                    <a href="{{ url('/menu') }}" class="menu-link">
                        <i class="fas fa-utensils"></i>
                        <span>Ver Menu Completo</span>
                    </a>

                    @php
                        $defaultLinks = [
                            ['name' => 'instagram', 'url' => 'https://instagram.com', 'target_blank' => true],
                        ];

                        $socialLinks = $hero->social_links ?: $defaultLinks;

                        $options = [
                            'facebook'  => ['icon' => 'fa-brands fa-facebook', 'color' => 'facebook-color'],
                            'instagram' => ['icon' => 'fa-brands fa-instagram', 'color' => 'instagram-color'],
                            'whatsapp'  => ['icon' => 'fa-brands fa-whatsapp', 'color' => 'whatsapp-color'],
                            'tiktok'    => ['icon' => 'fa-brands fa-tiktok', 'color' => 'tiktok-color'],
                        ];
                    @endphp

                    @foreach($socialLinks as $link)
                        @php
                            $name  = strtolower($link['name'] ?? 'link');
                            $url   = $link['url'] ?? '#';
                            $icon  = $options[$name]['icon'] ?? 'fa-solid fa-link';
                            $color = $options[$name]['color'] ?? 'social-default';
                            $blank = !empty($link['target_blank']) ? 'target="_blank"' : '';
                        @endphp

                        <a href="{{ $url }}" class="social-link {{ $color }}" {!! $blank !!}>
                            <i class="{{ $icon }}"></i>
                            <span>{{ ucfirst($name) }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        </main>

        <footer>
            <p>{{ $hero->footer_text ?? 'Magaf 2025 - Todos os direitos reservados' }}</p>
        </footer>
    </div>
</body>
</html>
