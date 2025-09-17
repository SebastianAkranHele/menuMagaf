<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Garrafeira</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-Z3s9fFz0ZqXkL1e6K4C1K0aMZ1VtK0uFq3yXb9J2B5Y5X3Yb7Z9Q9fY3Xj3F9F9F" crossorigin="anonymous">
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- FontAwesome (opcional, para ícones) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    {{-- CSS/JS globais --}}
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])

    {{-- CSS específico de cada página --}}
    @stack('styles')
</head>
<body>
    <!-- Botão Hamburger (aparece em telas pequenas) -->
    <button class="sidebar-toggle btn btn-outline-secondary">☰</button>
    <div class="sidebar-overlay"></div>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>Garrafeira Admin</h2>
            <p>Menu Digital</p>
        </div>
        <ul class="sidebar-menu list-unstyled">
            <li><a href="{{ route('admin.dashboard') }}" class="nav-link">Dashboard</a></li>
            <li><a href="{{ route('admin.home.index') }}" class="nav-link">Conteúdo da Home</a></li>
            <li><a href="{{ route('admin.categories.index') }}" class="nav-link">Categorias</a></li>
            <li><a href="{{ route('admin.products.index') }}" class="nav-link">Produtos</a></li>
            <li><a href="{{ route('admin.orders.index') }}" class="nav-link">Pedidos</a></li>

        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header d-flex justify-content-between align-items-center p-3">
            <h2>Painel Administrativo</h2>
            <div class="user-menu d-flex align-items-center gap-2">
                <span>Olá, Admin</span>
                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger">Sair</button>
                    </form>
            </div>
        </div>

        <div class="content p-3">
            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS Bundle (Popper + JS) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-X3yX9F3b4vZ9F3X3yF3X3F3yF3X3F3yF3X3F3yF3X3F3X3F3" crossorigin="anonymous"></script>
<!-- Bootstrap JS (dependência Popper incluída) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    {{-- JS específico de cada página --}}
    @stack('scripts')

    <!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const logoutForm = document.getElementById('logout-form');

    logoutForm.addEventListener('submit', function(e) {
        e.preventDefault(); // previne envio imediato

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
