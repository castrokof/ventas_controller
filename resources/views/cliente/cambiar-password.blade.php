<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cambiar contraseña</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
  * { box-sizing: border-box; }
  body {
    background: #f0f2f5; font-family: 'Segoe UI', system-ui, sans-serif; margin: 0;
    min-height: 100vh;
  }
  .portal-nav {
    background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);
    color: #fff; padding: .7rem 1rem;
    display: flex; align-items: center; justify-content: space-between;
  }
  .portal-nav .brand { font-weight: 700; font-size: .95rem; }
  .portal-nav a { color: rgba(255,255,255,.85); font-size: .82rem; text-decoration: none; }
  .portal-nav a:hover { color: #fff; }
  .card-form {
    max-width: 420px; margin: 2rem auto; background: #fff;
    border-radius: 14px; box-shadow: 0 4px 16px rgba(0,0,0,.1); overflow: hidden;
  }
  .card-form .card-header-custom {
    background: #1a237e; color: #fff; padding: .9rem 1.2rem;
    font-weight: 700; font-size: .95rem;
  }
  .card-form .card-body-custom { padding: 1.4rem 1.2rem; }
  .form-label { font-size: .85rem; font-weight: 600; color: #444; }
  .hint { font-size: .75rem; color: #888; margin-top: .2rem; }
  .btn-guardar { border-radius: 8px; font-weight: 700; }
  .show-pass { cursor: pointer; background: #f8f9fa; }
  .show-pass:hover { background: #e9ecef; }
</style>
</head>
<body>

{{-- Nav --}}
<div class="portal-nav">
  <div class="brand"><i class="fas fa-hand-holding-usd mr-1"></i>Portal Clientes</div>
  <div style="display:flex;gap:1rem">
    <a href="{{ route('cliente.portal.dashboard') }}"><i class="fas fa-arrow-left mr-1"></i>Volver</a>
    <a href="{{ route('cliente.portal.logout') }}"><i class="fas fa-sign-out-alt mr-1"></i>Salir</a>
  </div>
</div>

<div class="container px-3">
  <div class="card-form">
    <div class="card-header-custom">
      <i class="fas fa-lock mr-1"></i>Cambiar contraseña
    </div>
    <div class="card-body-custom">
      <p class="text-muted mb-3" style="font-size:.85rem">
        Hola, <strong>{{ $cliente->nombres }}</strong>. Elige una contraseña segura de al menos 6 caracteres.
      </p>

      @if($errors->any())
      <div class="alert alert-danger py-2" style="font-size:.85rem;border-radius:8px">
        <i class="fas fa-exclamation-circle mr-1"></i>{{ $errors->first() }}
      </div>
      @endif

      @if(session('success'))
      <div class="alert alert-success py-2" style="font-size:.85rem;border-radius:8px">
        <i class="fas fa-check-circle mr-1"></i>{{ session('success') }}
      </div>
      @endif

      <form method="POST" action="{{ route('cliente.portal.change_password') }}">
        @csrf

        <div class="form-group mb-3">
          <label class="form-label d-block mb-1">Nueva contraseña</label>
          <div class="input-group">
            <input type="password" name="nueva_password" id="inp-nueva"
                   class="form-control" placeholder="Mínimo 6 caracteres" required>
            <div class="input-group-append">
              <span class="input-group-text show-pass" onclick="togglePass('inp-nueva','eye1')">
                <i class="fas fa-eye" id="eye1" style="font-size:.82rem"></i>
              </span>
            </div>
          </div>
        </div>

        <div class="form-group mb-4">
          <label class="form-label d-block mb-1">Confirmar contraseña</label>
          <div class="input-group">
            <input type="password" name="confirmar_password" id="inp-confirma"
                   class="form-control" placeholder="Repite la contraseña" required>
            <div class="input-group-append">
              <span class="input-group-text show-pass" onclick="togglePass('inp-confirma','eye2')">
                <i class="fas fa-eye" id="eye2" style="font-size:.82rem"></i>
              </span>
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-guardar w-100">
          <i class="fas fa-save mr-1"></i>Guardar contraseña
        </button>
      </form>
    </div>
  </div>
</div>

<script>
function togglePass(id, iconId) {
    var inp  = document.getElementById(id);
    var icon = document.getElementById(iconId);
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        inp.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
</body>
</html>
