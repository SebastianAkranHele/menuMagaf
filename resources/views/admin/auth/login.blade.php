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
            background: linear-gradient(135deg, #e60d0d, #7b1fa2);
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
            border-radius: 15px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 20px rgba(0,0,0,0.2);
            animation: fadeIn 1s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .login-form h2 { text-align: center; margin-bottom: 0.5rem; color: var(--light); }
        .login-form p { text-align: center; margin-bottom: 1.5rem; color: #D7CCC8; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 500; }
        .form-group input {
            width: 100%;
            padding: 0.75rem 2.5rem 0.75rem 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--light);
            font-size: 1rem;
            transition: border 0.3s;
        }
        .form-group input:focus { outline: none; border-color: var(--accent); }
        .form-group.error input { border-color: #ff5252; }
        .btn {
            width: 100%;
            padding: 0.75rem;
            background: var(--accent);
            color: var(--text-dark);
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .btn:hover { background: #8A7169; transform: translateY(-2px); }
        .btn:disabled { opacity: 0.7; cursor: not-allowed; }
        .logo-container { text-align: center; margin-bottom: 1rem; }
        .logo-container img { max-width: 120px; border-radius: 50%; }
        .input-icon {
            position: relative;
        }
        .input-icon i {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #ccc;
        }
    </style>

    <!-- FontAwesome para ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

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

            <form method="POST" action="{{ route('admin.login.post') }}" id="loginForm">
                @csrf
                <div class="form-group input-icon">
                    <label for="username">Usuário:</label>
                    <input type="text" id="username" name="username" required autofocus>
                    <i class="fa fa-user"></i>
                </div>
                <div class="form-group input-icon">
                    <label for="password">Senha:</label>
                    <input type="password" id="password" name="password" required>
                    <i class="fa fa-lock"></i>
                </div>
                <button type="submit" class="btn" id="btnLogin">Entrar</button>
            </form>
        </div>
    </div>

    <script>
        // Loading no botão
        const form = document.getElementById("loginForm");
        const btn = document.getElementById("btnLogin");
        form.addEventListener("submit", () => {
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Entrando...';
            btn.disabled = true;
        });

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
