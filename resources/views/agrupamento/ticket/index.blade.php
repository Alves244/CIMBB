@extends('layouts.user_type.auth')

@section('content')
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header pb-0 d-flex flex-column flex-lg-row gap-3 align-items-lg-center justify-content-lg-between">
            <div>
              <h6 class="mb-0">Pedidos de Suporte</h6>
              <p class="text-sm mb-0">Agrupamento: {{ Auth::user()->agrupamento->nome ?? 'N/A' }}</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
              <x-admin.filter-modal modalId="agrupamentoTicketsFilter"
                                    :action="route('agrupamento.suporte.index')"
                                    :clear-url="route('agrupamento.suporte.index')"
                                    title="Filtrar pedidos">
                <div class="col-12">
                  <label class="form-label text-xs text-uppercase text-secondary mb-1">Estado</label>
                  <select name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="em_processamento" {{ request('estado') == 'em_processamento' ? 'selected' : '' }}>Por responder</option>
                    <option value="respondido" {{ request('estado') == 'respondido' ? 'selected' : '' }}>Respondidos</option>
                  </select>
                </div>
              </x-admin.filter-modal>
              <a href="{{ route('agrupamento.suporte.create') }}" class="btn bg-gradient-success btn-sm">
                <i class="fas fa-plus me-1"></i> Novo Ticket
              </a>
            </div>
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Código</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Assunto</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Categoria</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Data</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado</th>
                    <th class="text-secondary opacity-7">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($meusTickets as $ticket)
                    <tr>
                      <td><div class="d-flex px-3 py-1"><h6 class="mb-0 text-sm">{{ $ticket->codigo }}</h6></div></td>
                      <td><p class="text-xs font-weight-bold mb-0">{{ Str::limit($ticket->assunto, 40) }}</p></td>
                      <td class="align-middle text-center text-sm"><span class="badge badge-sm bg-gradient-secondary">{{ ucfirst($ticket->categoria) }}</span></td>
                      <td class="align-middle text-center text-sm"><span class="text-secondary text-xs font-weight-bold">{{ $ticket->created_at->format('d/m/Y H:i') }}</span></td>
                      <td class="align-middle text-center text-sm">
                        @if($ticket->estado == 'aberto')
                          <span class="badge badge-sm bg-gradient-warning">Aberto</span>
                        @elseif($ticket->estado == 'em_processamento')
                          <span class="badge badge-sm bg-gradient-info">Em Processamento</span>
                        @elseif($ticket->estado == 'respondido')
                          <span class="badge badge-sm bg-gradient-primary">Respondido</span>
                        @elseif($ticket->estado == 'resolvido')
                          <span class="badge badge-sm bg-gradient-success">Resolvido</span>
                        @else
                          <span class="badge badge-sm bg-gradient-light">{{ ucfirst(str_replace('_',' ', $ticket->estado)) }}</span>
                        @endif
                      </td>
                      <td class="align-middle">
                        <a href="{{ route('agrupamento.suporte.show', $ticket->id) }}" class="support-action-btn">Ver detalhes</a>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" class="text-center text-sm py-4">Ainda não submeteu nenhum ticket.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
          @if ($meusTickets->hasPages())
            <div class="card-footer px-3 border-0 d-flex align-items-center justify-content-between">
              {{ $meusTickets->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection

@push('css')
  <style>
    .support-action-btn {
      display: inline-block;
      padding: 0.1rem 0.25rem;
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.06em;
      color: #1e3358;
      text-decoration: none;
      transition: color 0.2s ease;
    }
    .support-action-btn:hover {
      color: #0f74c0;
      text-decoration: none;
    }
  </style>
@endpush
