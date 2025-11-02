<nav class="navbar navbar-main navbar-expand-lg px-0 mx-4 shadow-none border-radius-xl" id="navbarBlur" navbar-scroll="true">
    <div class="container-fluid py-1 px-3">
        {{-- Título da Página e Breadcrumbs (Isto fica) --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
            <li class="breadcrumb-item text-sm"><a class="opacity-5 text-dark" href="javascript:;">Página</a></li>
            {{-- Tenta usar o $title que passámos, ou usa o URL como fallback --}}
            <li class="breadcrumb-item text-sm text-dark active text-capitalize" aria-current="page">{{ $title ?? str_replace('-', ' ', Request::path()) }}</li>
            </ol>
            <h6 class="font-weight-bolder mb-0 text-capitalize">{{ $title ?? str_replace('-', ' ', Request::path()) }}</h6>
        </nav>

        {{-- Container dos itens da direita --}}
        <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4 d-flex justify-content-end" id="navbar"> 

            {{-- Lista de Itens da Navbar (Mantemos o Sign Out e o ícone mobile) --}}
            <ul class="navbar-nav justify-content-end">
                <li class="nav-item d-flex align-items-center">
                    {{-- Link de Sign Out (Mantido) --}}
                    <a href="{{ url('/logout')}}" class="nav-link text-body font-weight-bold px-0">
                        <i class="fa fa-user me-sm-1"></i>
                        <span class="d-sm-inline d-none">Terminar sessão</span>
                    </a>
                </li>
                <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                    {{-- Ícone "Hamburger" para mobile (Mantido) --}}
                    <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                        <div class="sidenav-toggler-inner">
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                            <i class="sidenav-toggler-line"></i>
                        </div>
                    </a>
                </li>
        </div>
    </div>
</nav>