<!DOCTYPE html>

@if (\Request::is('rtl'))
  <html dir="rtl" lang="ar">
@else
  <html lang="en" >
@endif

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  @if (env('IS_DEMO'))
      <x-demo-metas></x-demo-metas>
  @endif

  <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/img/apple-icon.png') }}">
  <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.png') }}">
  <title>
    SMRE Beira Baixa
  </title>
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet" />
  <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet" />
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
  <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet" />
  <link id="pagestyle" href="{{ asset('assets/css/soft-ui-dashboard.css?v=1.0.3') }}" rel="stylesheet" />


{{-- ========================================================= --}}
  {{--  CSS: BARRA LARGA, LOGO GRANDE, SEM SCROLL E SEM ESPAÇO   --}}
  {{-- ========================================================= --}}
  <style>
      /* 1. Aumentar a largura da Sidebar */
      .sidenav {
          width: 300px !important;
      }

      /* 2. Configuração do CABEÇALHO (LOGO) */
      .sidenav-header {
          /* Diminuí de 130px para 100px para tirar o espaço em branco */
          height: 100px !important; 
          margin-bottom: 0px !important; /* Removi a margem de baixo */
      }

      /* Aumentar a imagem do logo */
      .sidenav .navbar-brand-img {
          max-height: 90px !important; /* O logo ocupa quase a altura toda do cabeçalho */
          width: auto !important;
      }

      /* 3. Configuração da ÁREA DE MENUS */
      .sidenav .navbar-collapse {
          /* Ajuste do cálculo: 100vh - 100px (altura do cabeçalho) */
          height: calc(100vh - 100px) !important; 
          
          max-height: none !important;
          overflow: hidden !important; /* Sem scroll */
      }
      
      /* Remover padding extra no topo da lista se existir */
      .navbar-nav {
          margin-top: 0 !important;
          padding-top: 0 !important;
      }

      /* 4. Ajustar o conteúdo principal (Direita) */
      @media (min-width: 1200px) {
          .g-sidenav-show .main-content {
              margin-left: 315px !important;
          }
          .g-sidenav-show .navbar.fixed-top {
              left: 315px !important;
              width: calc(100% - 315px) !important;
          }
      }

      /* 5. Esconder scrollbars */
      .ps__rail-y, .ps__rail-x, .ps__thumb-y, .ps__thumb-x {
          display: none !important;
          opacity: 0 !important;
          width: 0 !important;
      }
  </style>

  @stack('css')

</head>

<body class="g-sidenav-show  bg-white {{ (\Request::is('rtl') ? 'rtl' : (Request::is('virtual-reality') ? 'virtual-reality' : '')) }} ">
  @auth
    @yield('auth')
  @endauth
  @guest
    @yield('guest')
  @endguest

  {{-- Mensagem de SUCESSO (Centrada, 5 segundos) --}}
  @if(session()->has('success'))
    <div x-data="{ show: true}"
        x-init="setTimeout(() => show = false, 5000)"
        x-show="show" x-transition
        class="position-fixed bg-gradient-success rounded top-3 start-50 translate-middle-x text-sm py-2 px-4 text-white" 
        style="z-index: 9999;">
      <p class="m-0">{{ session('success')}}</p>
    </div>
  @endif

  {{-- Mensagem de ERRO (Centrada, 5 segundos) --}}
  @if(session()->has('error') || $errors->any())
    <div x-data="{ show: true}"
         x-init="setTimeout(() => show = false, 5000)"
         x-show="show" x-transition
         class="position-fixed bg-gradient-danger rounded top-3 start-50 translate-middle-x text-sm py-2 px-4 text-white" 
         style="z-index: 9999;">
        <p class="m-0">{{ $errors->any() ? $errors->first() : session('error') }}</p>
    </div>
  @endif


    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/fullcalendar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>

  @stack('rtl')
  @stack('dashboard')
  <script>
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
      var options = {
        damping: '0.5'
      }
      Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }
  </script>

  <script async defer src="https://buttons.github.io/buttons.js"></script>
  <script src="{{ asset('assets/js/soft-ui-dashboard.min.js?v=1.0.3') }}"></script>

  {{-- O JavaScript extra (como o Choices.js) será "empurrado" aqui --}}
  @stack('js')
</body>

</html>