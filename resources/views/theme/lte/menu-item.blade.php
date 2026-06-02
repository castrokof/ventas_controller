@php
    $url        = $item['url'] ?? '#';
    $hasSubmenu = !empty($item['submenu']);

    // Extraer solo el path del URL almacenado (puede ser relativo o absoluto)
    $itemPath = '';
    if ($url !== '#' && $url !== '') {
        $parsed   = parse_url(url($url));
        $itemPath = ltrim($parsed['path'] ?? '', '/');
    }

    $currPath = request()->path(); // ej. "pagoc" o "admin/v2/cliente"

    // ¿Es este ítem el activo?
    $isActive = $itemPath !== '' && $currPath === $itemPath;

    // ¿Algún hijo directo está activo? (para abrir el treeview padre)
    $isOpen = false;
    if ($hasSubmenu && !$isActive) {
        foreach ($item['submenu'] as $sub) {
            $subUrl  = $sub['url'] ?? '';
            $subPath = '';
            if ($subUrl !== '#' && $subUrl !== '') {
                $p       = parse_url(url($subUrl));
                $subPath = ltrim($p['path'] ?? '', '/');
            }
            if ($subPath !== '' && $currPath === $subPath) {
                $isOpen = true;
                break;
            }
        }
    }
@endphp

@if (!$hasSubmenu)
<li class="nav-item">
    <a href="{{ url($url) }}" class="nav-link {{ $isActive ? 'active' : '' }}">
        <i class="nav-icon {{ $item['icono'] }}"></i>
        <p>{{ $item['nombre'] }}</p>
    </a>
</li>
@else
<li class="nav-item has-treeview {{ $isOpen ? 'menu-open' : '' }}">
    <a href="#" class="nav-link {{ $isOpen ? 'active' : '' }}">
        <i class="nav-icon {{ $item['icono'] }}"></i>
        <p>
            {{ $item['nombre'] }}
            <i class="right fas fa-angle-left"></i>
        </p>
    </a>
    <ul class="nav nav-treeview">
        @foreach ($item['submenu'] as $submenu)
            @include("theme.$theme.menu-item", ['item' => $submenu])
        @endforeach
    </ul>
</li>
@endif
