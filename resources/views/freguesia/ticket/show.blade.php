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
                                @elseif($ticket->estado == 'resolvido')
                                    <span class="badge badge-sm bg-gradient-success ms-1">Resolvido</span>
                                @else
                                    <span class="badge badge-sm bg-gradient-light ms-1">{{ $ticket->estado }}</span>
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

                        {{-- Card: A Minha Mensagem --}}
                        <div class="card card-body border card-plain border-radius-lg mb-3">
                            <label class="form-control-label">A Minha Mensagem</label>
                            <p class="text-sm">{{ $ticket->descricao }}</p>
                            @if($ticket->anexo)
                                <a href="{{ Storage::url($ticket->anexo) }}" target="_blank" class="btn btn-outline-secondary btn-sm mt-2" style="max-width: 150px;">
                                    <i class="fas fa-paperclip me-1"></i> Ver Anexo
                                </a>
                            @endif
                        </div>

                        {{-- Card: Resposta do Admin --}}
                        <div class="card card-body border card-plain border-radius-lg" 
                             style="background-color: #f8f9fa;"> {{-- Fundo cinza claro --}}
                            
                            <label class="form-control-label">Resposta do Suporte</label>

                            @if($ticket->resposta_admin)
                                <p class="text-sm mb-2">{{ $ticket->resposta_admin }}</p>
                                <hr class="horizontal dark my-2">
                                <span class="text-xs text-secondary">
                                    Respondido por: {{ $ticket->administrador->nome ?? 'Admin' }} 
                                    em {{ $ticket->data_resposta ? \Carbon\Carbon::parse($ticket->data_resposta)->format('d/m/Y H:i') : '' }}
                                </span>
                            @else
                                <p class="text-sm text-muted fst-italic">O seu pedido ainda não foi respondido.</p>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection