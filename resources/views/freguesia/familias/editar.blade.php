@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 col-lg-10 mx-auto">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Editar ficha de família residente</h6>
                        @if ($familia->updated_at)
                            <p class="text-xs text-muted mb-0">Última atualização: {{ $familia->updated_at->format('d/m/Y H:i') }}</p>
                        @endif
                        @if ($familia->estado_acompanhamento === 'desinstalada')
                            <p class="text-xs text-danger mb-0">Família desinstalada{{ $familia->data_desinstalacao ? ' desde '.$familia->data_desinstalacao->format('d/m/Y') : '' }}.</p>
                        @endif
                    </div>
                    <div class="card-body">
                        <form action="{{ route('freguesia.familias.update', $familia->id) }}" method="POST" role="form">
                            @csrf
                            @method('PUT')
                            @include('freguesia.familias.partials.form-fields', [
                                'familia' => $familia,
                                'nacionalidades' => $nacionalidades,
                                'formOptions' => $formOptions,
                                'setores' => $setores,
                                'submitLabel' => 'Guardar alterações'
                            ])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection