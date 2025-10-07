<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Garrafeira</title>

    {{-- Atualização automática opcional --}}
    @hasSection('auto-refresh')
        <meta http-equiv="refresh" content="@yield('auto-refresh', 30)">
    @endif

    <!-- ✅ Bootstrap 5.3.3 (versão única e correta) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">

    <!-- FontAwesome (ícones opcionais) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    {{-- CSS/JS globais do projeto --}}
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])

    {{-- CSS específico de cada página --}}
    @stack('styles')
</head>

<body>
    <!-- Botão Hamburger (para telas pequenas) -->
    <button class="sidebar-toggle btn btn-outline-secondary">☰</button>
    <div class="sidebar-overlay"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header text-center p-3 border-bottom">
            <h2>Garrafeira Admin</h2>
            <p class="text-muted">Menu Digital</p>
        </div>
        <ul class="sidebar-menu list-unstyled px-3 mt-3">
            <li><a href="{{ route('admin.dashboard') }}" class="nav-link">📊 Dashboard</a></li>
            <li><a href="{{ route('admin.home.index') }}" class="nav-link">🏠 Conteúdo da Home</a></li>
            <li><a href="{{ route('admin.categories.index') }}" class="nav-link">📂 Categorias</a></li>
            <li><a href="{{ route('admin.products.index') }}" class="nav-link">🍷 Produtos</a></li>
            <li><a href="{{ route('admin.orders.index') }}" class="nav-link">🧾 Pedidos</a></li>
            <li><a href="{{ route('admin.reports.index') }}" class="nav-link">📈 Relatórios</a></li>
            <li><a href="{{ route('admin.qrcode.index') }}" class="nav-link">🔗 QR Code</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header d-flex justify-content-between align-items-center p-3 border-bottom bg-light">
            <h2 class="m-0">Painel Administrativo</h2>
            <div class="user-menu d-flex align-items-center gap-2">
                <span>Olá, Admin</span>
                <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm">Sair</button>
                </form>
            </div>
        </div>

        <!-- Conteúdo Dinâmico -->
        <div class="content p-3">
            @yield('content')
        </div>
    </div>

    <!-- ✅ Bootstrap JS Bundle (com Popper incluído) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
            crossorigin="anonymous"></script>

    {{-- JS específico de cada página --}}
    @stack('scripts')

    <!-- SweetAlert2 (confirmação elegante de logout) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const logoutForm = document.getElementById('logout-form');

            logoutForm.addEventListener('submit', function (e) {
                e.preventDefault(); // impede envio imediato

                Swal.fire({
                    title: 'Deseja sair?',
                    text: "Você será desconectado do painel.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, sair',
                    cancelButtonText: 'Cancelar',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        logoutForm.submit(); // envia o formulário somente se confirmado
                    }
                });
            });
        });
    </script>
</body>

</html>
