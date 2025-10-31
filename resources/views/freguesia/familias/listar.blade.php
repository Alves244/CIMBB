@extends('layouts.app', ['class' => 'g-sidenav-show bg-gray-100'])

@section('auth')

    {{-- 2. ADICIONAR A SIDEBAR: (Caminho corrigido para 'auth.sidebar') --}}
    @include('layouts.navbars.auth.sidebar') {{-- <--- CORREÇÃO AQUI --}}

    {{-- 3. ADICIONAR O <main> WRAPPER: --}}
    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg {{ (Request::is('rtl') ? 'overflow-hidden' : 'overflow-auto') }}">

        {{-- Inclui a navbar do template (caminho 'auth.nav' estava correto) --}}
        @include('layouts.navbars.auth.nav', ['title' => 'Gerir Famílias'])

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        {{-- Cabeçalho do Card --}}
                        <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0">Famílias Registadas</h6>
                                <p class="text-sm">Freguesia: {{ Auth::user()->freguesia->nome ?? 'N/A' }}</p>
                            </div>
                            {{-- Botão Adicionar (aponta para a rota 'create') --}}
                            <a href="{{ route('freguesia.familias.create') }}" class="btn bg-gradient-success btn-sm mb-0">
                                <i class="fas fa-plus me-1"></i> Adicionar Família
                            </a>
                        </div>
                        {{-- Corpo do Card (Tabela) --}}
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    {{-- Cabeçalho da Tabela --}}
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Código</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ano Inst.</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Nacionalidade</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Membros</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Habitação</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Propriedade</th>
                                            <th class="text-secondary opacity-7">Ações</th>
                                        </tr>
                                    </thead>
                                    {{-- Corpo da Tabela --}}
                                    <tbody>
                                        @forelse ($familias as $familia)
                                            <tr>
                                                {{-- Código --}}
                                                <td>
                                                    <div class="d-flex px-3 py-1">
                                                        <h6 class="mb-0 text-sm">{{ $familia->codigo }}</h6>
                                                    </div>
                                                </td>
                                                {{-- Ano Instalação --}}
                                                <td class="align-middle text-center text-sm">
                                                    <span class="badge badge-sm bg-gradient-secondary">{{ $familia->ano_instalacao }}</span>
                                                </td>
                                                {{-- Nacionalidade --}}
                                                <td>
                                                     <p class="text-xs font-weight-bold mb-0">{{ $familia->nacionalidade }}</p>
                                                </td>
                                                {{-- Total Membros (do agregado familiar carregado) --}}
                                                <td class="align-middle text-center text-sm">
                                                    <span class="text-secondary text-xs font-weight-bold">{{ $familia->agregadoFamiliar?->total_membros ?? 'N/A' }}</span>
                                                </td>
                                                {{-- Tipologia Habitação --}}
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ ucfirst($familia->tipologia_habitacao) }}</p>
                                                </td>
                                                 {{-- Tipologia Propriedade --}}
                                                <td>
                                                    <p class="text-xs font-weight-bold mb-0">{{ ucfirst($familia->tipologia_propriedade) }}</p>
                                                </td>
                                                {{-- Ações --}}
                                                <td class="align-middle">
                                                    <a href="{{ route('freguesia.familias.show', $familia->id) }}" class="btn btn-link text-info text-gradient px-1 mb-0" data-bs-toggle="tooltip" data-bs-original-title="Ver Detalhes">
                                                        <i class="fas fa-eye text-sm"></i>
                                                    </a>
                                                    <a href="{{ route('freguesia.familias.edit', $familia->id) }}" class="btn btn-link text-secondary text-gradient px-1 mb-0" data-bs-toggle="tooltip" data-bs-original-title="Editar Família">
                                                        <i class="fas fa-pencil-alt text-sm"></i>
                                                    </a>
                                                    <form action="{{ route('freguesia.familias.destroy', $familia->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-link text-danger text-gradient px-1 mb-0"
                                                                onclick="return confirm('Tem a certeza que deseja apagar esta família (Código: {{ $familia->codigo }})? Esta ação não pode ser revertida.')"
                                                                data-bs-toggle="tooltip" data-bs-original-title="Apagar Família">
                                                            <i class="fas fa-trash text-sm"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center text-sm py-4">Nenhuma família registada para esta freguesia.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                         {{-- Paginação --}}
                         @if ($familias->hasPages())
                            <div class="card-footer px-3 border-0 d-flex align-items-center justify-content-between">
                                {{ $familias->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Inclui o footer do template --}}
            @include('layouts.footers.auth.footer')
        </div>
    </main>

@endsection

@push('js')
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush