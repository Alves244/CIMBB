<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 " id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    {{-- Link para o Dashboard principal, acessível a todos --}}
    <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ route('dashboard') }}">
      {{-- TODO: Alterar logo e nome da aplicação --}}
      <img src="{{ asset('assets/img/logo-ct.png') }}" class="navbar-brand-img h-100" alt="Logo CIMBB">
      <span class="ms-3 font-weight-bold">SMRE Beira Baixa</span>
    </a>
  </div>
  <hr class="horizontal dark mt-0">
  <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
    <ul class="navbar-nav">

      {{-- ===== MENU COMUM A TODOS OS UTILIZADORES LOGADOS ===== --}}
      <li class="nav-item">
        <a class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
            {{-- Ícone Dashboard (pode ser alterado) --}}
            <i class="fas fa-tachometer-alt ps-2 pe-2 text-center text-dark {{ Request::routeIs('dashboard') ? 'text-white' : 'text-dark' }}"></i>
          </div>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>

      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">A Minha Conta</h6>
      </li>
      <li class="nav-item">
        {{-- Link para a página de perfil (usando a rota 'profile' existente) --}}
        <a class="nav-link {{ Request::routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
             <i class="fas fa-user ps-2 pe-2 text-center text-dark {{ Request::routeIs('profile') ? 'text-white' : 'text-dark' }}"></i>
          </div>
          <span class="nav-link-text ms-1">Meu Perfil</span>
        </a>
      </li>
      {{-- Fim Menu Comum --}}


      {{-- ===== MENU ESPECÍFICO PARA FUNCIONÁRIO DA FREGUESIA ===== --}}
      @if(auth()->user()->isFreguesia())
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Gestão Freguesia</h6>
        </li>
        <li class="nav-item">
          {{-- TODO: Criar rota 'freguesia.familias.index' --}}
          <a class="nav-link {{ Request::routeIs('freguesia.familias.*') ? 'active' : '' }}" href="#"> {{-- href="{{ route('freguesia.familias.index') }}" --}}
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-users ps-2 pe-2 text-center text-dark {{ Request::routeIs('freguesia.familias.*') ? 'text-white' : 'text-dark' }}"></i>
            </div>
              <a class="nav-link {{ Request::routeIs('freguesia.familias.*') ? 'active' : '' }}" href="{{ route('freguesia.familias.index') }}">          </a>
        </li>
        <li class="nav-item">
            {{-- TODO: Criar rota 'freguesia.inqueritos.index' ou similar --}}
            <a class="nav-link {{ Request::routeIs('freguesia.inqueritos.*') ? 'active' : '' }}" href="#"> {{-- href="{{ route('freguesia.inqueritos.index') }}" --}}
              <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="fas fa-poll ps-2 pe-2 text-center text-dark {{ Request::routeIs('freguesia.inqueritos.*') ? 'text-white' : 'text-dark' }}"></i>
              </div>
              <span class="nav-link-text ms-1">Inquérito Anual</span>
            </a>
        </li>
         <li class="nav-item">
            {{-- TODO: Criar rota 'freguesia.tickets.index' --}}
            <a class="nav-link {{ Request::routeIs('freguesia.tickets.*') ? 'active' : '' }}" href="#"> {{-- href="{{ route('freguesia.tickets.index') }}" --}}
              <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="fas fa-question-circle ps-2 pe-2 text-center text-dark {{ Request::routeIs('freguesia.tickets.*') ? 'text-white' : 'text-dark' }}"></i>
              </div>
              <span class="nav-link-text ms-1">Suporte</span>
            </a>
        </li>
      @endif
      {{-- Fim Menu Freguesia --}}


      {{-- ===== MENU ESPECÍFICO PARA FUNCIONÁRIO CIMBB (E ADMIN) ===== --}}
      {{-- Usamos isFuncionario() porque o middleware 'funcionario' permite admin também --}}
      @if(auth()->user()->isFuncionario() || auth()->user()->isAdmin())
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Análise CIMBB</h6>
        </li>
        <li class="nav-item">
          {{-- TODO: Criar rota 'funcionario.dashboard.regional' ou adaptar a principal --}}
          <a class="nav-link {{ Request::routeIs('funcionario.dashboard.*') ? 'active' : '' }}" href="#"> {{-- href="{{ route('funcionario.dashboard.regional') }}" --}}
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-chart-line ps-2 pe-2 text-center text-dark {{ Request::routeIs('funcionario.dashboard.*') ? 'text-white' : 'text-dark' }}"></i>
            </div>
            <span class="nav-link-text ms-1">Dashboard Regional</span>
          </a>
        </li>
        <li class="nav-item">
            {{-- TODO: Criar rota 'funcionario.relatorios.index' --}}
            <a class="nav-link {{ Request::routeIs('funcionario.relatorios.*') ? 'active' : '' }}" href="#"> {{-- href="{{ route('funcionario.relatorios.index') }}" --}}
              <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="fas fa-file-alt ps-2 pe-2 text-center text-dark {{ Request::routeIs('funcionario.relatorios.*') ? 'text-white' : 'text-dark' }}"></i>
              </div>
              <span class="nav-link-text ms-1">Relatórios</span>
            </a>
        </li>
         <li class="nav-item">
            {{-- TODO: Criar rota 'funcionario.exportar.index' --}}
            <a class="nav-link {{ Request::routeIs('funcionario.exportar.*') ? 'active' : '' }}" href="#"> {{-- href="{{ route('funcionario.exportar.index') }}" --}}
              <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="fas fa-file-export ps-2 pe-2 text-center text-dark {{ Request::routeIs('funcionario.exportar.*') ? 'text-white' : 'text-dark' }}"></i>
              </div>
              <span class="nav-link-text ms-1">Exportar Dados</span>
            </a>
        </li>
      @endif
      {{-- Fim Menu CIMBB --}}


      {{-- ===== MENU ESPECÍFICO PARA ADMIN ===== --}}
      @if(auth()->user()->isAdmin())
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Administração</h6>
        </li>
        <li class="nav-item">
          {{-- Rota 'admin.user-management' do template --}}
          <a class="nav-link {{ Request::routeIs('admin.user-management') ? 'active' : '' }}" href="{{ route('admin.user-management') }}">
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-users-cog ps-2 pe-2 text-center text-dark {{ Request::routeIs('admin.user-management') ? 'text-white' : 'text-dark' }}"></i>
            </div>
            <span class="nav-link-text ms-1">Gerir Utilizadores</span>
          </a>
        </li>
         <li class="nav-item">
          {{-- TODO: Criar rota 'admin.concelhos.index' --}}
          <a class="nav-link {{ Request::routeIs('admin.concelhos.*') ? 'active' : '' }}" href="#"> {{-- href="{{ route('admin.concelhos.index') }}" --}}
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-map-marker-alt ps-2 pe-2 text-center text-dark {{ Request::routeIs('admin.concelhos.*') ? 'text-white' : 'text-dark' }}"></i>
            </div>
            <span class="nav-link-text ms-1">Gerir Concelhos</span>
          </a>
        </li>
        <li class="nav-item">
          {{-- TODO: Criar rota 'admin.freguesias.index' --}}
          <a class="nav-link {{ Request::routeIs('admin.freguesias.*') ? 'active' : '' }}" href="#"> {{-- href="{{ route('admin.freguesias.index') }}" --}}
            <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
              <i class="fas fa-map-pin ps-2 pe-2 text-center text-dark {{ Request::routeIs('admin.freguesias.*') ? 'text-white' : 'text-dark' }}"></i>
            </div>
            <span class="nav-link-text ms-1">Gerir Freguesias</span>
          </a>
        </li>
         <li class="nav-item">
            {{-- TODO: Criar rota 'admin.tickets.index' --}}
            <a class="nav-link {{ Request::routeIs('admin.tickets.*') ? 'active' : '' }}" href="#"> {{-- href="{{ route('admin.tickets.index') }}" --}}
              <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="fas fa-headset ps-2 pe-2 text-center text-dark {{ Request::routeIs('admin.tickets.*') ? 'text-white' : 'text-dark' }}"></i>
              </div>
              <span class="nav-link-text ms-1">Gerir Suporte</span>
            </a>
        </li>
         <li class="nav-item">
            {{-- TODO: Criar rota 'admin.logs.index' --}}
            <a class="nav-link {{ Request::routeIs('admin.logs.*') ? 'active' : '' }}" href="#"> {{-- href="{{ route('admin.logs.index') }}" --}}
              <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="fas fa-clipboard-list ps-2 pe-2 text-center text-dark {{ Request::routeIs('admin.logs.*') ? 'text-white' : 'text-dark' }}"></i>
              </div>
              <span class="nav-link-text ms-1">Logs do Sistema</span>
            </a>
        </li>
         <li class="nav-item">
            {{-- TODO: Criar rota 'admin.parametros.index' --}}
            <a class="nav-link {{ Request::routeIs('admin.parametros.*') ? 'active' : '' }}" href="#"> {{-- href="{{ route('admin.parametros.index') }}" --}}
              <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
                <i class="fas fa-cogs ps-2 pe-2 text-center text-dark {{ Request::routeIs('admin.parametros.*') ? 'text-white' : 'text-dark' }}"></i>
              </div>
              <span class="nav-link-text ms-1">Parâmetros</span>
            </a>
        </li>
      @endif
      {{-- Fim Menu Admin --}}


      {{-- ===== LINKS DO TEMPLATE ORIGINAL (REMOVER ou manter se útil) ===== --}}
      {{-- <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Example pages</h6>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ Request::routeIs('tables') ? 'active' : '' }}" href="{{ route('tables') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
             <i class="fas fa-table ps-2 pe-2 text-center text-dark {{ Request::routeIs('tables') ? 'text-white' : 'text-dark' }}"></i>
          </div>
          <span class="nav-link-text ms-1">Tables</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ Request::routeIs('billing') ? 'active' : '' }}" href="{{ route('billing') }}">
          <div class="icon icon-shape icon-sm shadow border-radius-md bg-white text-center me-2 d-flex align-items-center justify-content-center">
             <i class="fas fa-credit-card ps-2 pe-2 text-center text-dark {{ Request::routeIs('billing') ? 'text-white' : 'text-dark' }}"></i>
          </div>
          <span class="nav-link-text ms-1">Billing</span>
        </a>
      </li> --}}
      {{-- ... outros links de exemplo do template ... --}}
      {{-- REMOVER links de Sign In / Sign Up daqui, pois só fazem sentido para guest --}}

    </ul>
  </div>

  {{-- Secção Footer da Sidebar (Links Docs/Upgrade) - Manter ou Remover --}}
  <div class="sidenav-footer mx-3 ">
    {{-- ... (código original do footer da sidebar) ... --}}
  </div>
</aside>