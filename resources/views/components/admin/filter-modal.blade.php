@props([
    'modalId',
    'action',
    'title' => 'Filtrar resultados',
    'method' => 'GET',
    'triggerLabel' => 'Filtrar',
    'triggerClass' => 'btn btn-sm bg-gradient-secondary d-inline-flex align-items-center gap-2',
    'clearLabel' => 'Limpar filtros',
    'submitLabel' => 'Aplicar filtros',
    'clearUrl' => null,
    'size' => 'lg',
])

@php
    $httpMethod = strtoupper($method);
@endphp

<div>
  <button type="button" class="{{ $triggerClass }}" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
    <i class="fas fa-filter text-xs"></i>
    <span>{{ $triggerLabel }}</span>
  </button>

  <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-labelledby="{{ $modalId }}Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-{{ $size }}">
      <div class="modal-content">
        <form method="{{ in_array($httpMethod, ['GET', 'POST']) ? $httpMethod : 'POST' }}" action="{{ $action }}">
          @if($httpMethod !== 'GET')
            @csrf
          @endif

          @if(!in_array($httpMethod, ['GET', 'POST']))
            @method($httpMethod)
          @endif

          <div class="modal-header">
            <h5 class="modal-title" id="{{ $modalId }}Label">{{ $title }}</h5>
            <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>

          <div class="modal-body">
            <div class="row g-3">
              {{ $slot }}
            </div>
          </div>

          <div class="modal-footer flex-column flex-sm-row gap-2 justify-content-between align-items-stretch">
            <a href="{{ $clearUrl ?? $action }}" class="btn btn-outline-danger w-100">
              <i class="fas fa-undo me-1"></i> {{ $clearLabel }}
            </a>
            <div class="d-flex w-100 gap-2">
              <button type="button" class="btn bg-gradient-secondary flex-fill" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn bg-gradient-success flex-fill">{{ $submitLabel }}</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
