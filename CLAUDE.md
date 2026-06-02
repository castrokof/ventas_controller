# ventas_controller — Contexto del proyecto

## Stack
- **Laravel 5.x + PHP 8.4** | Producción: `manteliviano.com/coll_system/`
- **DB**: MySQL — tablas clave: `usuario`, `empleado`, `empresa`, `cliente`, `prestamo`, `detalle_prestamo`
- **Auth**: sesiones PHP — claves: `session('usuario_id')`, `session('rol_id')`, `session('rol_nombre')`, `session('empleado_id')`
- **DataTables**: Yajra `~9.0`

## Regla crítica
> **NO tocar** rutas, controladores ni vistas originales (sin prefijo V2).
> Todo desarrollo nuevo va en el módulo V2 o en rutas públicas separadas.

## Roles
| rol_id | rol_nombre    | Scope de datos                                |
|--------|---------------|-----------------------------------------------|
| 1      | administrador | Todo                                          |
| 2      | empresa       | Todos los usuarios de su empresa              |
| 3      | empleado      | Solo su propio `usuario_id`                   |

### Patrón `scopeUsuarioIds()` (en todos los controllers V2)
```php
private function scopeUsuarioIds(): array {
    $uid    = (int) session('usuario_id');
    $rol_id = (int) session('rol_id');
    if ($rol_id === 2) {
        $emp_id     = session('empleado_id');
        $empresa_id = DB::table('empleado')->where('ide', $emp_id)->value('empresa_id');
        if ($empresa_id) {
            $ids = DB::table('usuario')
                ->join('empleado','usuario.empleado_id','=','empleado.ide')
                ->where('empleado.empresa_id', $empresa_id)
                ->pluck('usuario.id')->toArray();
            return $ids ?: [$uid];
        }
    }
    return [$uid];
}
```

## Tablas clave

### `detalle_prestamo`
| campo             | descripción                        |
|-------------------|------------------------------------|
| `idd`             | PK                                 |
| `prestamo_id`     | FK → `prestamo.idp`                |
| `d_numero_cuota`  | Número de cuota                    |
| `valor_cuota`     | Valor a pagar                      |
| `valor_cuota_pagada` | Valor efectivamente pagado      |
| `fecha_cuota`     | Fecha de vencimiento               |
| `estado`          | `C`=Pendiente, `P`=Pagada, `A`=Atrasada, `T`=Saldada total |

### `prestamo`
| campo             | descripción                        |
|-------------------|------------------------------------|
| `idp`             | PK                                 |
| `estado`          | `P`=Pagado total, `A`=Anulado, vacío=Activo |
| `cliente_id`      | FK → `cliente.id`                  |
| `usuario_id`      | FK → `usuario.id`                  |

### Score / calificación de cliente
```
historial = pagadas + atrasadas
score     = round(pagadas / historial * 100) - min(atrasadas * 2, 20)
```
| score  | nivel | calificacion |
|--------|-------|--------------|
| ≥90 y 0 atrasadas | A | Excelente |
| ≥75    | B     | Bueno        |
| ≥55    | C     | Regular      |
| <55    | D     | Alto riesgo  |

## Módulo V2 — archivos principales

| Archivo | Descripción |
|---------|-------------|
| `app/Http/Controllers/Admin/V2/ClienteController.php`  | CRUD clientes + calificación |
| `app/Http/Controllers/Admin/V2/PrestamoController.php` | CRUD préstamos              |
| `app/Http/Controllers/Admin/V2/PagoController.php`     | Pago-card, cuotas, calendario |
| `app/Http/Controllers/Admin/V2/EmpleadoController.php` | CRUD empleados              |
| `app/Http/Controllers/ClientePortalController.php`     | Portal público clientes     |
| `resources/views/admin/v2/cliente/index.blade.php`     | Vista clientes V2           |
| `resources/views/admin/v2/pago_card/index.blade.php`   | Vista pago-card V2          |
| `resources/views/cliente/portal.blade.php`             | Portal auto-consulta clientes |
| `public/assets/pages/scripts/admin/v2/pago_card/calendar.js` | JS pago-card V2       |
| `resources/views/theme/lte/aside.blade.php`            | Menú lateral (dinámico por rol) |

## Rutas V2
- Prefijo: `admin/v2` | Middleware: `auth, superConsultor` | Namespace: `Admin\V2`
- Portal público: `GET /cliente-portal?documento=XXX` (sin auth)

## Reglas de negocio importantes
- Cuotas `T` (saldada total): NO mostrar en pago-card diario/calendario; SÍ en préstamos/historial
- Préstamos con `estado = 'P'` (pagado total): NO mostrar en panel de cobros del pago-card
- `pagocalender/index.js` debe quedar vacío (stub) — las vistas viejas lo cargan pero toda la lógica está inline

## Rama activa
`claude/modernize-laravel-parallel-NRhbb`
