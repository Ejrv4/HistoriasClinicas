@props([
    'label' => null,
    'name',
    'options' => [],
    'selectedValue' => null,
    'placeholder' => 'Escriba para buscar...',
    'uppercase' => false,
    'id' => null
])

@php
    $uniqueId = $id ?? 'dropdown_' . uniqid();
    $opcionInicial = collect($options)->first(function($opt) use ($selectedValue) {
        $val = isset($opt['id']) ? $opt['id'] : ($opt['value'] ?? '');
        return strval($val) === strval($selectedValue);
    });
    $textoInicial = $opcionInicial ? ($opcionInicial['nombre'] ?? $opcionInicial['label'] ?? '') : '';
@endphp

<div class="position-relative w-100 text-start custom-search-dropdown-box" id="{{ $uniqueId }}" data-uppercase="{{ $uppercase ? 'true' : 'false' }}">
    @if($label)
        <label class="form-label fw-bold text-muted small mb-1">{{ $label }}</label>
    @endif
    
    {{-- CAJA DE BÚSQUEDA SÓLIDA --}}
    <div class="position-relative">
        <input
            type="text"
            id="{{ $uniqueId }}_input"
            value="{{ $textoInicial }}"
            placeholder="{{ $placeholder }}"
            autocomplete="off"
            autocorrect="off"
            autocapitalize="off"
            spellcheck="false"
            class="form-control pe-5 {{ $uppercase ? 'text-uppercase' : '' }}"
            style="font-size: 0.9rem; font-weight: 500;"
        />
        <input type="hidden" name="{{ $name }}" id="{{ $uniqueId }}_value" value="{{ $selectedValue }}" class="real-hidden-value">

        {{-- Indicador de flecha controlado por estilos locales fijos --}}
        <div class="position-absolute end-0 top-50 translate-middle-y pe-3 pointer-events-none text-muted" style="line-height: 1;">
            <i class="bi bi-chevron-down dropdown-arrow-icon" id="{{ $uniqueId }}_arrow" style="transition: transform 0.2s; display: inline-block;"></i>
        </div>
    </div>

    {{-- LISTA FLOTANTE CON FILTRADO EN TIEMPO REAL --}}
    <ul id="{{ $uniqueId }}_list" class="d-none position-absolute start-0 w-100 dropdown-menu-floating shadow-lg border rounded-3 p-0 m-0 overflow-auto" style="max-height: 240px; z-index: 1050; background: #ffffff; list-style: none;">
        @if(count($options) === 0)
            <li class="px-3 py-2 text-muted fst-italic text-center small empty-placeholder">No hay opciones disponibles</li>
        @else
            @foreach($options as $index => $opt)
                @php
                    $valActual = isset($opt['id']) ? $opt['id'] : ($opt['value'] ?? '');
                    $nombrePintar = $opt['nombre'] ?? $opt['label'] ?? '';
                    $isSelected = strval($selectedValue) === strval($valActual);
                @endphp
                <li
                    data-value="{{ $valActual }}"
                    data-text="{{ $nombrePintar }}"
                    class="dropdown-item-custom position-relative px-3 py-2 border-bottom text-dark cursor-pointer text-truncate {{ $uppercase ? 'text-uppercase' : '' }} {{ $isSelected ? 'bg-primary text-white fw-bold' : '' }}"
                    style="font-size: 0.88rem; font-weight: 500; border-color: #f1f3f5 !important;"
                >
                    <span class="visible-text-span">{{ $nombrePintar }}</span>
                    
                    @if($isSelected)
                        <span class="float-end fw-bold ms-2 pe-1 checkmark-indicator">✓</span>
                    @endif

                    {{-- 🔥 TOOLTIP FLOTANTE INTEGRADO CONTRA TEXTOS LARGOS --}}
                    <div class="custom-hover-tooltip">
                        {{ $nombrePintar }}
                    </div>
                </li>
            @endforeach
        @endif
    </ul>
</div>

<script>
if (typeof window.initSingleCustomDropdown !== 'function') {
    window.initSingleCustomDropdown = function(containerId) {
        const container = document.getElementById(containerId);
        if (!container || container.hasAttribute('data-initialized')) return;
        
        container.setAttribute('data-initialized', 'true');
        const input = document.getElementById(containerId + '_input');
        const hiddenValue = document.getElementById(containerId + '_value');
        const list = document.getElementById(containerId + '_list');
        const arrow = document.getElementById(containerId + '_arrow');
        
        let lastSelectedText = input.value;

        // Evitar comportamientos destructivos en focus
        input.addEventListener('focus', () => {
            list.classList.remove('d-none');
            if(arrow) arrow.style.transform = 'rotate(180deg)';
            input.select();
            filtrar(input.value);
        });

        input.addEventListener('input', (e) => {
            filtrar(e.target.value);
            // Mostrar la lista al escribir si estaba oculta
            if (list.classList.contains('d-none')) {
                list.classList.remove('d-none');
                if(arrow) arrow.style.transform = 'rotate(180deg)';
            }
        });

        // ⌨️ NUEVA LÓGICA DE NAVEGACIÓN POR TECLADO
        input.addEventListener('keydown', (e) => {
            const visibleItems = Array.from(list.querySelectorAll('.dropdown-item-custom')).filter(item => item.style.display !== 'none');
            if (visibleItems.length === 0) return;

            let currentIndex = visibleItems.findIndex(item => item.classList.contains('active-keyboard-item'));

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (currentIndex < visibleItems.length - 1) currentIndex++;
                else currentIndex = 0;
                actualizarFocoTeclado(visibleItems, currentIndex);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (currentIndex > 0) currentIndex--;
                else currentIndex = visibleItems.length - 1;
                actualizarFocoTeclado(visibleItems, currentIndex);
            } else if (e.key === 'Tab' || e.key === 'Enter') {
                // Si la lista está visible y hay opciones filtradas, autoselecciona la activa o la primera
                if (!list.classList.contains('d-none')) {
                    e.preventDefault();
                    const itemSeleccionar = currentIndex >= 0 ? visibleItems[currentIndex] : visibleItems[0];
                    if (itemSeleccionar) {
                        seleccionarItem(itemSeleccionar);
                        // Permitir la propagación del Tab para saltar al siguiente input de la UI
                        if (e.key === 'Tab') {
                            const focusables = Array.from(document.querySelectorAll('input:not([type="hidden"]):not([disabled]), textarea:not([disabled]), select:not([disabled]), button:not([disabled])'));
                            const idx = focusables.indexOf(input);
                            if (idx >= 0 && idx < focusables.length - 1) {
                                focusables[idx + 1].focus();
                            }
                        }
                    }
                }
            }
        });

        function actualizarFocoTeclado(items, index) {
            items.forEach(i => i.classList.remove('active-keyboard-item'));
            if (items[index]) {
                items[index].classList.add('active-keyboard-item');
                items[index].scrollIntoView({ block: 'nearest' });
            }
        }

        function filtrar(term) {
            const cleanTerm = term.toLowerCase().trim();
            const items = list.querySelectorAll('.dropdown-item-custom');
            items.forEach(item => {
                const txt = item.getAttribute('data-text').toLowerCase();
                if (txt.includes(cleanTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                    item.classList.remove('active-keyboard-item');
                }
            });
        }

        function seleccionarItem(item) {
            const val = item.getAttribute('data-value');
            const text = item.getAttribute('data-text');

            if (hiddenValue.value !== val) {
                hiddenValue.value = val;
                input.value = text;
                lastSelectedText = text;

                const items = list.querySelectorAll('.dropdown-item-custom');
                items.forEach(i => i.classList.remove('bg-primary', 'text-white', 'fw-bold', 'active-keyboard-item'));
                item.classList.add('bg-primary', 'text-white', 'fw-bold');

                hiddenValue.dispatchEvent(new Event('change', { bubbles: true }));
            }

            list.classList.add('d-none');
            if(arrow) arrow.style.transform = 'rotate(0deg)';
        }

        list.addEventListener('click', (e) => {
            const item = e.target.closest('.dropdown-item-custom');
            if (!item) return;
            seleccionarItem(item);
        });

        // Cerrar al hacer clic fuera de manera segura
        document.addEventListener('mousedown', (e) => {
            if (!container.contains(e.target)) {
                list.classList.add('d-none');
                if(arrow) arrow.style.transform = 'rotate(0deg)';
                if (input.value.trim() === '') {
                    input.value = lastSelectedText;
                }
            }
        });
    };
}

document.addEventListener('DOMContentLoaded', () => {
    const parentBox = document.getElementById('{{ $uniqueId }}');
    const isDynamicRow = parentBox ? parentBox.closest('.diagnostico-row') : null;
    if (!isDynamicRow) {
        window.initSingleCustomDropdown('{{ $uniqueId }}');
    }
});
</script>

<style>
    .custom-search-dropdown-box { margin-bottom: 0.5rem; }
    .dropdown-menu-floating { top: 100%; margin-top: 4px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1) !important; border-color: #e9ecef !important; }
    .dropdown-item-custom { cursor: pointer; transition: background-color 0.15s, color 0.15s; user-select: none; }
    .dropdown-item-custom:hover, .dropdown-item-custom.active-keyboard-item { background-color: #0d6efd !important; color: #ffffff !important; }
    
    /* CONTROL DEL TOOLTIP OSCURO */
    .custom-hover-tooltip {
        position: absolute; inset: 0; padding: 0.5rem 1rem;
        background-color: #212529; color: #ffffff;
        font-size: 0.78rem; font-weight: 600; line-height: 1.3;
        display: flex; align-items: center; justify-content: start;
        pointer-events: none; visibility: hidden; opacity: 0;
        transition: opacity 0.1s ease; white-space: normal; z-index: 10;
    }
    .dropdown-item-custom:hover .custom-hover-tooltip { visibility: visible; opacity: 1; }
</style>