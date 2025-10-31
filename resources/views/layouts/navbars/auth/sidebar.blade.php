<aside class="sidenav navbar navbar-vertical navbar-expand-xs border-0 border-radius-xl my-3 fixed-start ms-3 " id="sidenav-main">
  <div class="sidenav-header">
    <i class="fas fa-times p-3 cursor-pointer text-secondary opacity-5 position-absolute end-0 top-0 d-none d-xl-none" aria-hidden="true" id="iconSidenav"></i>
    <a class="align-items-center d-flex m-0 navbar-brand text-wrap" href="{{ route('dashboard') }}">
      <img src="{{ asset('assets/img/cimbb/logo/logo-cimbb.png') }}" class="navbar-brand-img" style="height: 50px; width: auto;" alt="Logo SMRE Beira Baixa">
    </a>
  </div>
  <hr class="horizontal dark mt-0">
  <div class="collapse navbar-collapse w-auto" id="sidenav-collapse-main">
    <ul class="navbar-nav">

      {{-- ===== MENU COMUM ===== --}}
      <li class="nav-item">
        <a class="nav-link {{ Request::routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
          {{-- MUDANÇA: Usar style="" para forçar o verde --}}
          <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center {{ Request::routeIs('dashboard') ? '' : 'bg-white' }}"
               style="{{ Request::routeIs('dashboard') ? 'background-image: linear-gradient(310deg, #82d616 0%, #4ca800 100%) !important;' : '' }}">
            <i class="fas fa-tachometer-alt ps-2 pe-2 text-center {{ Request::routeIs('dashboard') ? 'text-white' : 'text-dark' }}"></i>
          </div>
          <span class="nav-link-text ms-1">Dashboard</span>
        </a>
      </li>

      <li class="nav-item mt-3">
        <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">A Minha Conta</h6>
      </li>
      <li class="nav-item">
        <a class="nav-link {{ Request::routeIs('profile') ? 'active' : '' }}" href="{{ route('profile') }}">
          {{-- MUDANÇA: Usar style="" para forçar o verde --}}
          <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center {{ Request::routeIs('profile') ? '' : 'bg-white' }}"
               style="{{ Request::routeIs('profile') ? 'background-image: linear-gradient(310deg, #82d616 0%, #4ca800 100%) !important;' : '' }}">
             <i class="fas fa-user ps-2 pe-2 text-center {{ Request::routeIs('profile') ? 'text-white' : 'text-dark' }}"></i>
          </div>
          <span class="nav-link-text ms-1">Meu Perfil</span>
        </a>
      </li>
      {{-- Fim Menu Comum --}}


      {{-- ===== MENU FREGUESIA ===== --}}
      @if(auth()->user()->isFreguesia())
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Gestão Freguesia</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ Request::routeIs('freguesia.familias.*') ? 'active' : '' }}" href="{{ route('freguesia.familias.index') }}">
            {{-- MUDANÇA: Usar style="" para forçar o verde --}}
            <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center {{ Request::routeIs('freguesia.familias.*') ? '' : 'bg-white' }}"
                 style="{{ Request::routeIs('freguesia.familias.*') ? 'background-image: linear-gradient(310deg, #82d616 0%, #4ca800 100%) !important;' : '' }}">
              <i class="fas fa-users ps-2 pe-2 text-center {{ Request::routeIs('freguesia.familias.*') ? 'text-white' : 'text-dark' }}"></i>
            </div>
            <span class="nav-link-text ms-1">Gerir Famílias</span>
          </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::routeIs('freguesia.inqueritos.*') ? 'active' : '' }}" href="#">
              {{-- MUDANÇA: Usar style="" para forçar o verde --}}
              <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center {{ Request::routeIs('freguesia.inqueritos.*') ? '' : 'bg-white' }}"
                   style="{{ Request::routeIs('freguesia.inqueritos.*') ? 'background-image: linear-gradient(310deg, #82d616 0%, #4ca800 100%) !important;' : '' }}">
                <i class="fas fa-poll ps-2 pe-2 text-center {{ Request::routeIs('freguesia.inqueritos.*') ? 'text-white' : 'text-dark' }}"></i>
              </div>
              <span class="nav-link-text ms-1">Inquérito Anual</span>
            </a>
        </li>
         <li class="nav-item">
            <a class="nav-link {{ Request::routeIs('freguesia.tickets.*') ? 'active' : '' }}" href="#">
              {{-- MUDANÇA: Usar style="" para forçar o verde --}}
              <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center {{ Request::routeIs('freguesia.tickets.*') ? '' : 'bg-white' }}"
                   style="{{ Request::routeIs('freguesia.tickets.*') ? 'background-image: linear-gradient(310deg, #82d616 0%, #4ca800 100%) !important;' : '' }}">
                <i class="fas fa-question-circle ps-2 pe-2 text-center {{ Request::routeIs('freguesia.tickets.*') ? 'text-white' : 'text-dark' }}"></i>
              </div>
              <span class="nav-link-text ms-1">Suporte</span>
            </a>
        </li>
      @endif
      {{-- Fim Menu Freguesia --}}


      {{-- ===== MENU CIMBB ===== --}}
      @if(auth()->user()->isFuncionario() || auth()->user()->isAdmin())
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Análise CIMBB</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ Request::routeIs('funcionario.dashboard.*') ? 'active' : '' }}" href="#">
            {{-- MUDANÇA: Usar style="" para forçar o verde --}}
            <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center {{ Request::routeIs('funcionario.dashboard.*') ? '' : 'bg-white' }}"
                 style="{{ Request::routeIs('funcionario.dashboard.*') ? 'background-image: linear-gradient(310deg, #82d616 0%, #4ca800 100%) !important;' : '' }}">
              <i class="fas fa-chart-line ps-2 pe-2 text-center {{ Request::routeIs('funcionario.dashboard.*') ? 'text-white' : 'text-dark' }}"></i>
            </div>
            <span class="nav-link-text ms-1">Dashboard Regional</span>
          </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ Request::routeIs('funcionario.relatorios.*') ? 'active' : '' }}" href="#">
              {{-- MUDANÇA: Usar style="" para forçar o verde --}}
              <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center {{ Request::routeIs('funcionario.relatorios.*') ? '' : 'bg-white' }}"
                   style="{{ Request::routeIs('funcionario.relatorios.*') ? 'background-image: linear-gradient(310deg, #82d616 0%, #4ca800 100%) !important;' : '' }}">
                <i class="fas fa-file-alt ps-2 pe-2 text-center {{ Request::routeIs('funcionario.relatorios.*') ? 'text-white' : 'text-dark' }}"></i>
              </div>
              <span class="nav-link-text ms-1">Relatórios</span>
            </a>
        </li>
         <li class="nav-item">
            <a class="nav-link {{ Request::routeIs('funcionario.exportar.*') ? 'active' : '' }}" href="#">
              {{-- MUDANÇA: Usar style="" para forçar o verde --}}
              <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center {{ Request::routeIs('funcionario.exportar.*') ? '' : 'bg-white' }}"
                   style="{{ Request::routeIs('funcionario.exportar.*') ? 'background-image: linear-gradient(310deg, #82d616 0%, #4ca800 100%) !important;' : '' }}">
                <i class="fas fa-file-export ps-2 pe-2 text-center {{ Request::routeIs('funcionario.exportar.*') ? 'text-white' : 'text-dark' }}"></i>
              </div>
              <span class="nav-link-text ms-1">Exportar Dados</span>
            </a>
        </li>
      @endif
      {{-- Fim Menu CIMBB --}}


      {{-- ===== MENU ADMIN ===== --}}
      @if(auth()->user()->isAdmin())
        <li class="nav-item mt-3">
          <h6 class="ps-4 ms-2 text-uppercase text-xs font-weight-bolder opacity-6">Administração</h6>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ Request::routeIs('admin.user-management') ? 'active' : '' }}" href="{{ route('admin.user-management') }}">
            {{-- MUDANÇA: Usar style="" para forçar o verde --}}
            <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center {{ Request::routeIs('admin.user-management') ? '' : 'bg-white' }}"
                 style="{{ Request::routeIs('admin.user-management') ? 'background-image: linear-gradient(310deg, #82d616 0%, #4ca800 100%) !important;' : '' }}">
              <i class="fas fa-users-cog ps-2 pe-2 text-center {{ Request::routeIs('admin.user-management') ? 'text-white' : 'text-dark' }}"></i>
            </div>
            <span class="nav-link-text ms-1">Gerir Utilizadores</span>
          </a>
        </li>
         <li class="nav-item">
          <a class="nav-link {{ Request::routeIs('admin.concelhos.*') ? 'active' : '' }}" href="#">
            {{-- MUDANÇA: Usar style="" para forçar o verde --}}
            <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center {{ Request::routeIs('admin.concelhos.*') ? '' : 'bg-white' }}"
                 style="{{ Request::routeIs('admin.concelhos.*') ? 'background-image: linear-gradient(310deg, #82d616 0%, #4ca800 100%) !important;' : '' }}">
              <i class="fas fa-map-marker-alt ps-2 pe-2 text-center {{ Request::routeIs('admin.concelhos.*') ? 'text-white' : 'text-dark' }}"></i>
            </div>
            <span class="nav-link-text ms-1">Gerir Concelhos</span>
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link {{ Request::routeIs('admin.freguesias.*') ? 'active' : '' }}" href="#">
            {{-- MUDANÇA: Usar style="" para forçar o verde --}}
            <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center {{ Request::routeIs('admin.freguesias.*') ? '' : 'bg-white' }}"
                 style="{{ Request::routeIs('admin.freguesias.*') ? 'background-image: linear-gradient(310deg, #82d616 0%, #4ca800 100%) !important;' : '' }}">
              <i class="fas fa-map-pin ps-2 pe-2 text-center {{ Request::routeIs('admin.freguesias.*') ? 'text-white' : 'text-dark' }}"></i>
            </div>
            <span class="nav-link-text ms-1">Gerir Freguesias</span>
          </a>
        </li>
         <li class="nav-item">
            <a class="nav-link {{ Request::routeIs('admin.tickets.*') ? 'active' : '' }}" href="#">
              {{-- MUDANÇA: Usar style="" para forçar o verde --}}
              <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center {{ Request::routeIs('admin.tickets.*') ? '' : 'bg-white' }}"
                   style="{{ Request::routeIs('admin.tickets.*') ? 'background-image: linear-gradient(310deg, #82d616 0%, #4ca800 100%) !important;' : '' }}">
                <i class="fas fa-headset ps-2 pe-2 text-center {{ Request::routeIs('admin.tickets.*') ? 'text-white' : 'text-dark' }}"></i>
              </div>
              <span class="nav-link-text ms-1">Gerir Suporte</span>
            </a>
        </li>
         <li class="nav-item">
            <a class="nav-link {{ Request::routeIs('admin.logs.*') ? 'active' : '' }}" href="#">
              {{-- MUDANÇA: Usar style="" para forçar o verde --}}
              <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center {{ Request::routeIs('admin.logs.*') ? '' : 'bg-white' }}"
                   style="{{ Request::routeIs('admin.logs.*') ? 'background-image: linear-gradient(310deg, #82d616 0%, #4ca800 100%) !important;' : '' }}">
                <i class="fas fa-clipboard-list ps-2 pe-2 text-center {{ Request::routeIs('admin.logs.*') ? 'text-white' : 'text-dark' }}"></i>
              </div>
              <span class="nav-link-text ms-1">Logs do Sistema</span>
            </a>
        </li>
         <li class="nav-item">
            <a class="nav-link {{ Request::routeIs('admin.parametros.*') ? 'active' : '' }}" href="#">
              {{-- MUDANÇA: Usar style="" para forçar o verde --}}
              <div class="icon icon-shape icon-sm shadow border-radius-md text-center me-2 d-flex align-items-center justify-content-center {{ Request::routeIs('admin.parametros.*') ? '' : 'bg-white' }}"
                   style="{{ Request::routeIs('admin.parametros.*') ? 'background-image: linear-gradient(310deg, #82d616 0%, #4ca800 100%) !important;' : '' }}">
                <i class="fas fa-cogs ps-2 pe-2 text-center {{ Request::routeIs('admin.parametros.*') ? 'text-white' : 'text-dark' }}"></i>
              </div>
              <span class="nav-link-text ms-1">Parâmetros</span>
            </a>
        </li>
      @endif
      {{-- Fim Menu Admin --}}

    </ul>
  </div>

  {{-- Secção Footer da Sidebar (Ocultada) --}}
  <div class="sidenav-footer mx-3 " style="display: none;">
    {{-- ... --}}
  </div>
</aside>