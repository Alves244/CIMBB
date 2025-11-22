@extends('layouts.user_type.auth')



@section('content')

<div class="container-fluid py-4">

  <div class="row">

    <div class="col-12">

      <div class="card mb-4">

        <div class="card-header pb-0 d-flex flex-column flex-xl-row gap-3 align-items-xl-center justify-content-xl-between">

          <div>

            <h6>Gestão de Suporte (Todos os Tickets)</h6>

            <p class="text-sm">Lista de pedidos de suporte de todas as freguesias.</p>

          </div>

          <form method="GET" action="{{ route('admin.tickets.index') }}" class="d-flex flex-wrap gap-2">

            <div class="input-group input-group-sm">

              <span class="input-group-text bg-white">Estado</span>

              <select name="estado" class="form-select">

                <option value="">Todos</option>

                <option value="em_processamento" {{ request('estado') == 'em_processamento' ? 'selected' : '' }}>Por responder</option>

                <option value="respondido" {{ request('estado') == 'respondido' ? 'selected' : '' }}>Respondidos</option>

              </select>

            </div>

            <button type="submit" class="btn btn-sm bg-gradient-secondary">Filtrar</button>

          </form>

        </div>

        <div class="card-body px-0 pt-0 pb-2">

          <div class="table-responsive p-0">

            <table class="table align-items-center mb-0">

              <thead>

                <tr>

                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Código / Freguesia</th>

                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Assunto</th>

                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Categoria</th>

                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estado</th>

                  <th class="text-secondary opacity-7">Ações</th>

                </tr>

              </thead>

              <tbody>

                @forelse($tickets as $ticket)

                  <tr>

                    <td>

                      <div class="d-flex px-3 py-1">

                        <div class="d-flex flex-column justify-content-center">

                          <h6 class="mb-0 text-sm">{{ $ticket->codigo }}</h6>

                          <p class="text-xs text-secondary mb-0">

                              {{ $ticket->utilizador->freguesia->nome ?? 'Sem Freguesia' }}

                              <br>

                              <span class="text-xxs">({{ $ticket->utilizador->nome }})</span>

                          </p>

                        </div>

                      </div>

                    </td>

                    <td>

                      <p class="text-xs font-weight-bold mb-0">{{ Str::limit($ticket->assunto, 30) }}</p>

                      <p class="text-xs text-secondary mb-0">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>

                    </td>

                    <td class="align-middle text-center text-sm">

                      <span class="badge badge-sm bg-gradient-secondary">{{ ucfirst($ticket->categoria) }}</span>

                    </td>

                    <td class="align-middle text-center text-sm">

                        @php

                          $badges = [

                            'aberto' => 'warning',

                            'em_processamento' => 'info',

                            'respondido' => 'primary',

                            'resolvido' => 'success',

                            'fechado' => 'secondary'

                          ];

                          // Remove underscores para exibição

                          $estadoLabel = ucfirst(str_replace('_', ' ', $ticket->estado));

                        @endphp

                        <span class="badge badge-sm bg-gradient-{{ $badges[$ticket->estado] ?? 'light' }}">

                            {{ $estadoLabel }}

                        </span>

                    </td>

                    <td class="align-middle">

                        <a href="{{ route('admin.tickets.show', $ticket->id) }}" class="btn btn-link text-dark px-3 mb-0">

                            <i class="fas fa-reply me-2"></i> Responder

                        </a>

                    </td>

                  </tr>

                @empty

                  <tr><td colspan="5" class="text-center py-4">Não há tickets registados no sistema.</td></tr>

                @endforelse

              </tbody>

            </table>

          </div>

        </div>

        @if ($tickets->hasPages())

            <div class="card-footer px-3 border-0 d-flex justify-content-center">

                {{ $tickets->links() }}

            </div>

        @endif

      </div>

    </div>

  </div>

</div>

@endsection