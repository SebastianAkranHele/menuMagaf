<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Painel do Cliente')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            background: #f5f5f5;
            min-height: 100vh;
        }
        .navbar-client {
            background-color: #e60d0d;
        }
        .navbar-client .navbar-brand,
        .navbar-client .nav-link {
            color: #fff;
        }
        .sidebar {
            background-color: #7b1fa2;
            min-height: 100vh;
            padding-top: 20px;
        }
        .sidebar a {
            display: block;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            margin-bottom: 5px;
        }
        .sidebar a:hover {
            background-color: #e60d0d;
            border-radius: 5px;
        }
        .content {
            padding: 20px;
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-client">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ route('client.dashboard') }}">Cliente</a>
            <div class="d-flex">
                <form method="POST" action="{{ route('client.logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm">Sair <i class="fa fa-sign-out-alt"></i></button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <a href="{{ route('client.dashboard') }}"><i class="fa fa-home"></i> Dashboard</a>
                <a href="{{ route('client.home.index') }}"><i class="fa fa-image"></i> Hero Home</a>
                <a href="{{ route('client.categories.index') }}"><i class="fa fa-list"></i> Categorias</a>
                <a href="{{ route('client.products.index') }}"><i class="fa fa-box"></i> Produtos</a>
            </div>

            <!-- Conteúdo principal -->
            <div class="col-md-10 content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
