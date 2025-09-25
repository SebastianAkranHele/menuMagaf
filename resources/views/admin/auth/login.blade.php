<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Garrafeira Admin</title>
    <style>
        :root {
            --primary: #e60d0d;
            --accent: #A1887F;
            --light: #F5F5F5;
            --text-dark: #3E2723;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--primary);
            color: var(--light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container { width: 100%; max-width: 400px; padding: 20px; }
        .login-form {
            background: rgba(255, 255, 255, 0.1);
            padding: 2rem;
            border-radius: 10px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .login-form h2 { text-align: center; margin-bottom: 0.5rem; color: var(--light); }
        .login-form p { text-align: center; margin-bottom: 1.5rem; color: #D7CCC8; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--light);
            font-size: 1rem;
        }
        .form-group input:focus { outline: none; border-color: var(--accent); }
        .btn {
            width: 100%;
            padding: 0.75rem;
            background: var(--accent);
            color: var(--text-dark);
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn:hover { background: #8A7169; transform: translateY(-2px); }
        .logo-container { text-align: center; margin-bottom: 1rem; }
        .logo-container img { max-width: 150px; }
    </style>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="login-container">
        <div class="login-form">

            <!-- Logo da empresa -->
            <div class="logo-container">
              <img src="{{ asset('assets/magaf1.jpg') }}" alt="Garrafeira Logo">

            </div>

            <h2>Garrafeira das 5 Curvas</h2>
            <p>Painel Administrativo</p>

            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf
                <div class="form-group">
                    <label for="username">Usuário:</label>
                    <input type="text" id="username" name="username" required autofocus>
                </div>
                <div class="form-group">
                    <label for="password">Senha:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn">Entrar</button>
            </form>
        </div>
    </div>

    <script>
        // Mensagem de sucesso
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Bem-vindo!',
                text: '{{ session('success') }}',
                confirmButtonColor: '#A1887F'
            });
        @endif

        // Mensagens de erro
        @if($errors->any())
            @foreach ($errors->all() as $error)
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: '{{ $error }}',
                    confirmButtonColor: '#e60d0d'
                });
            @endforeach
        @endif
    </script>
</body>
</html>
