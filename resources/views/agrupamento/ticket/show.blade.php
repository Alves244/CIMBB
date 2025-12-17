@extends('layouts.user_type.auth')

@section('content')
  <div class="container-fluid py-4">
    <div class="row">
      <div class="col-12 col-lg-8 mx-auto">
        <div class="card">
          <div class="card-header pb-0 d-flex justify-content-between align-items-center">
            <div>
              <h6 class="mb-0">Ticket {{ $ticket->codigo }}</h6>
              <p class="text-sm mb-0">Assunto: {{ $ticket->assunto }}</p>
            </div>
            <a href="{{ route('agrupamento.suporte.index') }}" class="btn btn-secondary btn-sm">
              <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
          </div>
          <div class="card-body">
            <div class="row mb-3">
              <div class="col-md-4">
                <span class="text-xs text-uppercase text-secondary">Estado</span>
                <div>
                  @if($ticket->estado == 'aberto')
                    <span class="badge bg-gradient-warning">Aberto</span>
                  @elseif($ticket->estado == 'em_processamento')
                    <span class="badge bg-gradient-info">Em Processamento</span>
                  @elseif($ticket->estado == 'respondido')
                    <span class="badge bg-gradient-primary">Respondido</span>
                  @elseif($ticket->estado == 'resolvido')
                    <span class="badge bg-gradient-success">Resolvido</span>
                  @else
                    <span class="badge bg-gradient-light">{{ ucfirst(str_replace('_',' ', $ticket->estado)) }}</span>
                  @endif
                </div>
              </div>
              <div class="col-md-4">
                <span class="text-xs text-uppercase text-secondary">Categoria</span>
                <p class="text-sm mb-0">{{ ucfirst($ticket->categoria) }}</p>
              </div>
              <div class="col-md-4">
                <span class="text-xs text-uppercase text-secondary">Criado em</span>
                <p class="text-sm mb-0">{{ $ticket->created_at->format('d/m/Y H:i') }}</p>
              </div>
            </div>

            @if($ticket->anexo)
              <div class="alert alert-light border text-sm d-flex align-items-center gap-2">
                <i class="fas fa-paperclip text-secondary"></i>
                <a href="{{ Storage::url($ticket->anexo) }}" target="_blank" class="text-decoration-underline">Ver anexo enviado</a>
              </div>
            @endif

            <h6 class="mt-4 mb-3 text-uppercase text-xs text-secondary">Histórico de mensagens</h6>
            <div class="d-flex flex-column gap-3">
              @forelse($ticket->mensagens as $mensagem)
                <div class="card card-body border border-radius-lg {{ $mensagem->autor && $mensagem->autor->isAdmin() ? 'bg-gray-100' : '' }}">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                    <div>
                      <span class="text-sm font-weight-bold">{{ $mensagem->autor->nome ?? 'Utilizador' }}</span>
                      @if($mensagem->autor)
                        <span class="badge badge-sm {{ $mensagem->autor->isAdmin() ? 'bg-gradient-dark' : 'bg-gradient-success' }} ms-2">
                          {{ $mensagem->autor->isAdmin() ? 'Suporte CIMBB' : 'Agrupamento' }}
                        </span>
                      @endif
                    </div>
                    <span class="text-xs text-secondary">{{ $mensagem->created_at->format('d/m/Y H:i') }}</span>
                  </div>
                  <p class="text-sm mb-0" style="white-space: pre-line;">{{ $mensagem->mensagem }}</p>
                </div>
              @empty
                <p class="text-sm text-muted">Ainda não existem mensagens neste ticket.</p>
              @endforelse
            </div>

            @if($podeResponder)
              <hr class="horizontal dark mt-4 mb-3">
              <form action="{{ route('agrupamento.suporte.mensagens.store', $ticket->id) }}" method="POST">
                @csrf
                <div class="form-group">
                  <label for="mensagem" class="form-control-label">Enviar nova mensagem</label>
                  <textarea class="form-control" id="mensagem" name="mensagem" rows="4" required>{{ old('mensagem') }}</textarea>
                </div>
                <button type="submit" class="btn bg-gradient-success mt-3">Enviar</button>
              </form>
            @else
              <div class="alert alert-secondary mt-4 mb-0">
                Este ticket está fechado. Abra um novo pedido se precisar de mais ajuda.
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
