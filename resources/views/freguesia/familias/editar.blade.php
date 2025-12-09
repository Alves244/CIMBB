@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 col-lg-10 mx-auto">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Editar ficha de família residente</h6>
                        <p class="text-sm mb-0">Código interno: {{ $familia->codigo }}</p>
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