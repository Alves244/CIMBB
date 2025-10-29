@extends('layouts.app') {{-- Ou o nome do teu layout principal --}}

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Famílias da Freguesia: {{ Auth::user()->freguesia->nome ?? 'N/A' }}</h6>
                        {{-- TODO: Adicionar botão para criar nova família --}}
                        {{-- <a href="{{ route('freguesia.familias.create') }}" class="btn btn-primary btn-sm ms-auto">Adicionar Família</a> --}}
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Código</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Ano Instalação</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nacionalidade</th>
                                        {{-- Adicionar mais colunas se necessário --}}
                                        <th class="text-secondary opacity-7"></th> {{-- Coluna para Ações --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($familias as $familia)
                                        <tr>
                                            <td>
                                                <div class="d-flex px-3 py-1">
                                                    <h6 class="mb-0 text-sm">{{ $familia->codigo }}</h6>
                                                </div>
                                            </td>
                                            <td>
                                                <p class="text-xs font-weight-bold mb-0">{{ $familia->ano_instalacao }}</p>
                                            </td>
                                            <td>
                                                 <p class="text-xs font-weight-bold mb-0">{{ $familia->nacionalidade }}</p>
                                            </td>
                                            {{-- TODO: Adicionar mais colunas se necessário --}}
                                            <td class="align-middle">
                                                {{-- TODO: Adicionar botões Ver/Editar/Apagar --}}
                                                {{-- <a href="{{ route('freguesia.familias.show', $familia->id) }}" class="text-secondary font-weight-bold text-xs" data-toggle="tooltip" data-original-title="Ver família">Ver</a> --}}
                                                {{-- <a href="{{ route('freguesia.familias.edit', $familia->id) }}" class="text-secondary font-weight-bold text-xs ms-2" data-toggle="tooltip" data-original-title="Editar família">Editar</a> --}}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-sm">Nenhuma família registada nesta freguesia.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{-- Links de Paginação --}}
                        <div class="card-footer py-4">
                             {{ $familias->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection