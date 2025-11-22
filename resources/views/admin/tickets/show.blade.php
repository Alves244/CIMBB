@extends('layouts.user_type.auth')



@section('content')

<div class="container-fluid py-4">

    <div class="row">

        <div class="col-lg-8 mx-auto">

            <div class="card mb-4">

                <div class="card-header pb-0">

                    <div class="d-flex justify-content-between">

                        <div>

                            <h6>Responder ao Ticket: {{ $ticket->codigo }}</h6>

                            <span class="badge bg-gradient-secondary">{{ ucfirst($ticket->categoria) }}</span>

                        </div>

                        <a href="{{ route('admin.tickets.index') }}" class="btn btn-sm btn-outline-secondary">Voltar</a>

                    </div>

                    <p class="text-sm mt-2 mb-0">

                        Enviado por: <strong>{{ $ticket->utilizador->nome ?? 'N/A' }}</strong> 

                        ({{ $ticket->utilizador->freguesia->nome ?? 'S/ Freguesia' }}) 

                        em {{ $ticket->created_at->format('d/m/Y H:i') }}

                    </p>

                </div>

                <div class="card-body">

                    

                    {{-- Mensagem Original --}}

                    <div class="bg-gray-100 border-radius-lg p-3 mb-4 border">

                        <h6 class="text-sm font-weight-bold mb-2">Descrição do Problema:</h6>

                        <p class="text-sm text-dark mb-0" style="white-space: pre-line;">{{ $ticket->descricao }}</p>

                        

                        @if($ticket->anexo)

                            <div class="mt-3 pt-3 border-top">

                                <a href="{{ Storage::url($ticket->anexo) }}" target="_blank" class="btn btn-xs btn-outline-dark mb-0">

                                    <i class="fas fa-paperclip me-1"></i> Ver Anexo Enviado

                                </a>

                            </div>

                        @endif

                    </div>

                    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Histórico de Mensagens</h6>

                    <div class="d-flex flex-column gap-3 mb-4">

                        @forelse($ticket->mensagens as $mensagem)

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

                            <p class="text-sm text-muted">Ainda não existem mensagens registadas para este ticket.</p>

                        @endforelse

                    </div>


                    {{-- Formulário de Resposta --}}

                    <h6 class="text-uppercase text-body text-xs font-weight-bolder mb-3">Enviar Mensagem ao Solicitante</h6>

                    

                    <form action="{{ route('admin.tickets.reply', $ticket->id) }}" method="POST">

                        @csrf

                        @method('PUT')



                        <div class="form-group">

                            <label for="mensagem" class="form-control-label">Mensagem</label>

                            <textarea class="form-control" name="mensagem" rows="6" required placeholder="Escreva a solução ou resposta aqui...">{{ old('mensagem') }}</textarea>

                            @error('mensagem')

                                <small class="text-danger">{{ $message }}</small>

                            @enderror

                        </div>



                        <div class="row">

                            <div class="col-md-6">

                                <div class="form-group">

                                    <label for="estado" class="form-control-label">Definir Estado do Ticket</label>

                                    <select class="form-control" name="estado">

                                        <option value="respondido" {{ $ticket->estado == 'respondido' ? 'selected' : '' }}>Respondido</option>

                                        <option value="resolvido" {{ $ticket->estado == 'resolvido' ? 'selected' : '' }}>Resolvido</option>

                                        <option value="fechado" {{ $ticket->estado == 'fechado' ? 'selected' : '' }}>Fechado</option>

                                    </select>

                                    @error('estado')

                                        <small class="text-danger">{{ $message }}</small>

                                    @enderror

                                </div>

                            </div>

                            <div class="col-md-6 text-end mt-4">

                                <button type="submit" class="btn bg-gradient-success w-100">

                                    <i class="fas fa-paper-plane me-1"></i> Enviar Resposta

                                </button>

                            </div>

                        </div>

                    </form>



                    @if($ticket->administrador)

                        <div class="alert alert-light text-xs mt-3 mb-0 text-center">

                            Última resposta por: <strong>{{ $ticket->administrador->nome }}</strong> em {{ \Carbon\Carbon::parse($ticket->data_resposta)->format('d/m/Y H:i') }}

                        </div>

                    @endif



                </div>

            </div>

        </div>

    </div>

</div>

@endsection