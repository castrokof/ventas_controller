<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Portal Clientes — Acceso</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
  * { box-sizing: border-box; }
  body {
    background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);
    min-height: 100vh;
    display: flex; align-items: center; justify-content: center;
    font-family: 'Segoe UI', system-ui, sans-serif;
    padding: 1rem;
  }
  .login-card {
    background: #fff; border-radius: 18px;
    box-shadow: 0 12px 40px rgba(0,0,0,.25);
    width: 100%; max-width: 400px;
    overflow: hidden;
  }
  .login-header {
    background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);
    color: #fff; padding: 2rem 1.5rem 1.5rem; text-align: center;
  }
  .login-header .icon {
    width: 64px; height: 64px; border-radius: 50%;
    background: rgba(255,255,255,.15);
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto .8rem; font-size: 1.8rem;
  }
  .login-header h1 { font-size: 1.3rem; font-weight: 700; margin: 0; }
  .login-header p  { margin: .3rem 0 0; opacity: .8; font-size: .85rem; }
  .login-body { padding: 1.8rem 1.5rem; }
  .form-label { font-size: .85rem; font-weight: 600; color: #444; }
  .input-icon { position: relative; }
  .input-icon i {
    position: absolute; left: .85rem; top: 50%; transform: translateY(-50%);
    color: #aaa; font-size: .85rem;
  }
  .input-icon input { padding-left: 2.3rem; }
  .btn-login {
    width: 100%; padding: .7rem; font-size: 1rem; font-weight: 700;
    border-radius: 10px; letter-spacing: .03em;
  }
  .hint-box {
    background: #f0f4ff; border: 1px solid #c5cae9; border-radius: 10px;
    padding: .7rem .9rem; font-size: .78rem; color: #3949ab; margin-top: 1rem;
  }
  .hint-box i { margin-right: .35rem; }
  .error-box {
    background: #fdecea; border: 1px solid #f5c6cb; border-radius: 10px;
    padding: .7rem .9rem; font-size: .82rem; color: #721c24; margin-bottom: 1rem;
  }
  .show-pass { cursor: pointer; color: #aaa; }
  .show-pass:hover { color: #555; }
</style>
</head>
<body>

<div class="login-card">
  <div class="login-header">
    <div class="icon"><i class="fas fa-hand-holding-usd"></i></div>
    <h1>Portal de Clientes</h1>
    <p>Consulta el estado de tus créditos</p>
  </div>

  <div class="login-body">
    @if($errors->any())
    <div class="error-box">
      <i class="fas fa-exclamation-circle"></i>
      {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('cliente.portal.login') }}">
      @csrf

      <div class="form-group mb-3">
        <label class="form-label d-block mb-1">
          <i class="fas fa-id-card mr-1 text-primary"></i>Número de documento
        </label>
        <div class="input-icon">
          <i class="fas fa-id-card"></i>
          <input type="number" name="documento" class="form-control"
                 placeholder="Ej: 10234567890"
                 value="{{ old('documento') }}"
                 required autofocus
                 style="border-radius:8px">
        </div>
      </div>

      <div class="form-group mb-3">
        <label class="form-label d-block mb-1">
          <i class="fas fa-lock mr-1 text-primary"></i>Contraseña
        </label>
        <div class="input-icon input-group">
          <div class="input-group-prepend">
            <span class="input-group-text" style="border-radius:8px 0 0 8px;background:#f8f9fa">
              <i class="fas fa-lock text-muted" style="font-size:.85rem"></i>
            </span>
          </div>
          <input type="password" name="password" id="inp-password"
                 class="form-control" placeholder="Tu contraseña"
                 required style="border-radius:0">
          <div class="input-group-append">
            <span class="input-group-text show-pass" id="btn-show-pass"
                  style="border-radius:0 8px 8px 0;background:#f8f9fa"
                  title="Mostrar/ocultar">
              <i class="fas fa-eye" id="icon-eye" style="font-size:.85rem"></i>
            </span>
          </div>
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-login mt-1">
        <i class="fas fa-sign-in-alt mr-1"></i>Ingresar
      </button>
    </form>

    <div class="hint-box">
      <i class="fas fa-info-circle"></i>
      <strong>Primera vez:</strong> tu contraseña son los últimos <strong>6 dígitos</strong>
      de tu número de documento.
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script>
document.getElementById('btn-show-pass').addEventListener('click', function() {
    var inp  = document.getElementById('inp-password');
    var icon = document.getElementById('icon-eye');
    if (inp.type === 'password') {
        inp.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        inp.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
});
</script>
</body>
</html>
