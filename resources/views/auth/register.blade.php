<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Soccer Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .card-register { width: 100%; max-width: 500px; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="card card-register bg-white">
    <div class="text-center mb-4">
        <h3>Crear Cuenta</h3>
        <p class="text-muted">Únete a Soccer App</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="row mb-3">
            <div class="col-6">
                <label class="form-label">Nombre</label>
                <input type="text" class="form-control" name="first_name" required autofocus value="{{ old('first_name') }}">
            </div>
            <div class="col-6">
                <label class="form-label">Apellido</label>
                <input type="text" class="form-control" name="last_name" required value="{{ old('last_name') }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Correo Electrónico</label>
            <input type="email" class="form-control" name="email" required value="{{ old('email') }}">
            @error('email')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Contraseña</label>
            <input type="password" class="form-control" name="password" required>
            <small class="text-muted">Mínimo 8 caracteres</small>
        </div>

        <div class="mb-3">
            <label class="form-label">Confirmar Contraseña</label>
            <input type="password" class="form-control" name="password_confirmation" required>
        </div>

        <div class="d-grid gap-2 mb-3">
            <button type="submit" class="btn btn-success">Registrarse</button>
        </div>

        <div class="text-center">
            <small>¿Ya tienes cuenta? <a href="{{ route('login') }}" class="text-decoration-none">Inicia Sesión</a></small>
        </div>
    </form>
</div>

</body>
</html>