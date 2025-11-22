@extends('layouts.user_type.auth')

@section('content')

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 col-lg-8 mx-auto">
                <div class="card">
                    <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0">Ticket: {{ $ticket->codigo }}</h6>
                            <p class="text-sm">Assunto: {{ $ticket->assunto }}</p>
                        </div>
                        <a href="{{ route('freguesia.suporte.index') }}" class="btn btn-secondary btn-sm mb-0">
                            <i class="fas fa-arrow-left me-1"></i> Voltar à Lista
                        </a>
                    </div>
                    <div class="card-body">
                        
                        {{-- Detalhes do Pedido --}}
                        <div class="row">
                            <div class="col-md-4">
                                <span class="text-xs text-uppercase font-weight-bold">Estado:</span>
                                @if($ticket->estado == 'aberto')
                                    <span class="badge badge-sm bg-gradient-warning ms-1">Aberto</span>
                                @elseif($ticket->estado == 'em_processamento')
                                    <span class="badge badge-sm bg-gradient-info ms-1">Em Processamento</span>
                                @elseif($ticket->estado == 'respondido')
                                    <span class="badge badge-sm bg-gradient-primary ms-1">Respondido</span>
                                @elseif($ticket->estado == 'resolvido')
                                    <span class="badge badge-sm bg-gradient-success ms-1">Resolvido</span>
                                @else
                                    <span class="badge badge-sm bg-gradient-light ms-1">{{ ucfirst(str_replace('_',' ', $ticket->estado)) }}</span>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <span class="text-xs text-uppercase font-weight-bold">Categoria:</span>
                                <span class="text-sm ms-1">{{ ucfirst($ticket->categoria) }}</span>
                            </div>
                             <div class="col-md-4">
                                <span class="text-xs text-uppercase font-weight-bold">Data do Pedido:</span>
                                <span class="text-sm ms-1">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>

                        <hr class="horizontal dark mt-4">

                        @if($ticket->anexo)
                            <div class="alert alert-light border text-sm d-flex align-items-center gap-2">
                                <i class="fas fa-paperclip text-secondary"></i>
                                <a href="{{ Storage::url($ticket->anexo) }}" target="_blank" class="text-decoration-underline">Ver anexo enviado</a>
                            </div>
                        @endif

                        <h6 class="mt-4 mb-3 text-uppercase text-xs text-secondary">Historico de Mensagens</h6>
                        <div class="d-flex flex-column gap-3">
                            @forelse ($ticket->mensagens as $mensagem)
                                <div class="card card-body border border-radius-lg {{ $mensagem->autor && $mensagem->autor->isAdmin() ? 'bg-gray-100' : '' }}">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div>
                                            <span class="text-sm font-weight-bold">{{ $mensagem->autor->nome ?? 'Utilizador' }}</span>
                                            @if($mensagem->autor)
                                                <span class="badge badge-sm {{ $mensagem->autor->isAdmin() ? 'bg-gradient-dark' : 'bg-gradient-success' }} ms-2">
                                                    {{ $mensagem->autor->isAdmin() ? 'Suporte CIMBB' : 'Freguesia' }}
                                                </span>
                                            @endif
                                        </div>
                                        <span class="text-xs text-secondary">{{ $mensagem->created_at->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <p class="text-sm mb-0" style="white-space: pre-line;">{{ $mensagem->mensagem }}</p>
                                </div>
                            @empty
                                <p class="text-sm text-muted">Ainda não existem mensagens associadas a este ticket.</p>
                            @endforelse
                        </div>

                        @if($podeResponder)
                            <hr class="horizontal dark mt-4 mb-3">
                            <form action="{{ route('freguesia.suporte.mensagens.store', $ticket->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="mensagem" class="form-control-label">Enviar nova mensagem</label>
                                    <textarea class="form-control" id="mensagem" name="mensagem" rows="4" required>{{ old('mensagem') }}</textarea>
                                    @error('mensagem')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <button type="submit" class="btn bg-gradient-success mt-3">Enviar para o Suporte</button>
                            </form>
                        @else
                            <div class="alert alert-secondary mt-4 mb-0">
                                Este ticket encontra-se fechado. Abra um novo ticket se precisar de mais assistência.
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection