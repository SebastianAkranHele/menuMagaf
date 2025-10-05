<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $hero->title ?? 'MAGAF - Menu Digital' }}</title>

    {{-- Atualização automática opcional --}}
    @hasSection('auto-refresh')
        <meta http-equiv="refresh" content="@yield('auto-refresh', 30)">
    @endif

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
            <!-- Hero Section -->
            <section class="hero"
                style="{{ $hero->background_image ? 'background: url(' . asset('storage/' . $hero->background_image) . ') no-repeat center center; background-size: cover; background-position: center; height: 100vh;' : '' }}">
            </section>

            <!-- Profile Section -->
            <section class="profile">
                <img src="{{ $hero->profile_image ? asset('storage/' . $hero->profile_image) : asset('assets/qrcode-magavi (4).png') }}"
                    alt="Perfil" class="profile-img">
                <div class="profile-content">
                    <h2>{{ $hero->profile_title ?? 'Experimente o sabor autêntico' }}</h2>
                    <p>{{ $hero->profile_subtitle ?? 'O ponto de referência' }}</p>
                </div>
            </section>

            <!-- Social Links Section -->
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
                            'facebook' => ['icon' => 'fa-brands fa-facebook', 'color' => 'facebook-color'],
                            'instagram' => ['icon' => 'fa-brands fa-instagram', 'color' => 'instagram-color'],
                            'whatsapp' => ['icon' => 'fa-brands fa-whatsapp', 'color' => 'whatsapp-color'],
                            'tiktok' => ['icon' => 'fa-brands fa-tiktok', 'color' => 'tiktok-color'],
                        ];
                    @endphp

                    @foreach ($socialLinks as $link)
                        @php
                            $name = strtolower($link['name'] ?? 'link');
                            $url = $link['url'] ?? '#';
                            $icon = $options[$name]['icon'] ?? 'fa-solid fa-link';
                            $color = $options[$name]['color'] ?? 'social-default';
                            $blank = !empty($link['target_blank']) ? 'target="_blank"' : '';
                        @endphp

                        <a href="{{ $url }}" class="social-link {{ $color }}" {!! $blank !!}>
                            <i class="{{ $icon }}"></i>
                            <span>{{ ucfirst($name) }}</span>
                        </a>
                    @endforeach

                    <!-- 🔹 Novo botão Área Administrativa -->
                    <button id="btnAdminAccess" class="social-link admin-color">
                        <i class="fas fa-user-shield"></i>
                        <span>Área Administrativa</span>
                    </button>
                </div>
            </section>
        </main>


        <!-- Modal para código de acesso -->
        <div id="adminAccessModal" class="modal hidden">
            <div class="modal-content">
                <h2 style="color: black; font-weight: bold;">Digite o código de acesso</h2>
                <input type="password" id="adminCode" placeholder="Código" class="input-code">
                <div class="modal-actions">
                    <button id="verifyCodeBtn" class="btn-confirm">Entrar</button>
                    <button id="closeModalBtn" class="btn-cancel">Cancelar</button>
                </div>
                <p id="errorMsg" class="error-msg hidden">Código incorreto!</p>
            </div>
        </div>

        <footer>
            <p>{{ $hero->footer_text ?? 'Magaf ' . date('Y') . ' - Todos os direitos reservados' }}</p>
        </footer>

    </div>

    @if (session('logout_success'))
        <div class="logout-alert" id="logoutAlert">
            {{ session('logout_success') }}
        </div>
    @endif

</body>

</html>
