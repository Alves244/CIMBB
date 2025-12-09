@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12 col-lg-10 mx-auto">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Nova ficha de família residente</h6>
                        <p class="text-sm">Freguesia: {{ Auth::user()->freguesia->nome ?? '-' }}</p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('freguesia.familias.store') }}" method="POST" role="form">
                            @csrf
                            @include('freguesia.familias.partials.form-fields', [
                                'familia' => null,
                                'nacionalidades' => $nacionalidades,
                                'formOptions' => $formOptions,
                                'setores' => $setores,
                                'submitLabel' => 'Guardar família'
                            ])
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
