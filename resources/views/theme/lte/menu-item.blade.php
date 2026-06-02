@php
    $url        = $item['url'] ?? '#';
    $hasSubmenu = !empty($item['submenu']);

    // request()->is() compara contra el path relativo al root de la app,
    // sin importar si está instalada en un subdirectorio.
    $isActive = $url !== '#' && $url !== ''
        && request()->is(ltrim($url, '/'));

    // ¿Algún hijo directo está activo? → abrir el treeview padre
    $isOpen = false;
    if ($hasSubmenu && !$isActive) {
        foreach ($item['submenu'] as $sub) {
            $subUrl = $sub['url'] ?? '';
            if ($subUrl !== '#' && $subUrl !== ''
                && request()->is(ltrim($subUrl, '/'))) {
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
    <ul class="nav nav-treeview" @if($isOpen) style="display:block" @endif>
        @foreach ($item['submenu'] as $submenu)
            @include("theme.$theme.menu-item", ['item' => $submenu])
        @endforeach
    </ul>
</li>
@endif
