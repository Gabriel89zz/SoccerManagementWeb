<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Soccer Manager</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card-login { width: 100%; max-width: 400px; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="card card-login bg-white">
    <div class="text-center mb-4">
        <h3>⚽ Soccer App</h3>
        <p class="text-muted">Inicia sesión para continuar</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" id="email" name="email" required autofocus>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>

        <!-- Mensaje de Error -->
        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="d-grid gap-2 mb-3">
            <button type="submit" class="btn btn-primary">Ingresar</button>
        </div>

        <!-- ENLACE DE REGISTRO (NUEVO) -->
        <div class="text-center">
            <small>¿No tienes cuenta? <a href="{{ route('register') }}" class="text-decoration-none">Regístrate aquí</a></small>
        </div>
    </form>
</div>

</body>
</html>