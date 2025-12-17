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

        .flash-container {
          position: fixed;
          top: 24px;
          left: 50%;
          transform: translateX(-50%);
          display: flex;
          flex-direction: column;
          gap: 0.75rem;
          z-index: 1200;
          width: min(92vw, 460px);
          pointer-events: none;
        }

        .flash-message {
          display: flex;
          align-items: flex-start;
          gap: 0.75rem;
          padding: 0.95rem 1.2rem;
          border-radius: 16px;
          color: #fff;
          font-size: 0.95rem;
          line-height: 1.45;
          box-shadow: 0 18px 35px rgba(15, 23, 42, 0.2);
          background: #1f2937;
          pointer-events: auto;
          animation: flashSlideIn 0.35s ease;
        }

        .flash-icon {
          font-size: 1.2rem;
          line-height: 1;
        }

        .flash-text {
          flex: 1;
        }

        .flash-close {
          background: transparent;
          border: 0;
          color: inherit;
          font-size: 1.2rem;
          line-height: 1;
          margin-left: 0.35rem;
          cursor: pointer;
          opacity: 0.8;
        }

        .flash-close:hover {
          opacity: 1;
        }

          .flash-message.flash-success {
            background: linear-gradient(135deg, #7ee084, #34d399);
          }

        .flash-message.flash-warning {
          background: linear-gradient(135deg, #f2994a, #f2c94c);
        }

        .flash-message.flash-info {
          background: linear-gradient(135deg, #396afc, #2948ff);
        }

        .flash-message.flash-error {
          background: linear-gradient(135deg, #eb3349, #f45c43);
        }

        .flash-hide {
          opacity: 0;
          transform: translateY(-8px) scale(0.98);
          transition: opacity 0.3s ease, transform 0.3s ease;
        }

        @keyframes flashSlideIn {
          from {
            opacity: 0;
            transform: translateY(-10px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
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

    @php
      $flashMessages = [];
      $sessionFlashMap = [
        'success' => 'success',
        'status' => 'info',
        'info' => 'info',
        'warning' => 'warning',
        'error' => 'error',
      ];

      foreach ($sessionFlashMap as $sessionKey => $type) {
        if (!session()->has($sessionKey)) {
          continue;
        }

          $message = session($sessionKey);
          if (is_array($message)) {
            $message = implode(' ', $message);
          }

        $flashMessages[] = [
          'type' => $type,
            'message' => $message,
        ];
      }

      if ($errors->any()) {
        foreach ($errors->all() as $message) {
          $flashMessages[] = [
            'type' => 'error',
            'message' => $message,
          ];
        }
      }
    @endphp

    @if (!empty($flashMessages))
      <div class="flash-container" role="status" aria-live="polite">
        @foreach ($flashMessages as $flash)
          @php
            $iconClass = match ($flash['type']) {
              'success' => 'fa-check-circle',
              'warning' => 'fa-exclamation-circle',
              'error' => 'fa-times-circle',
              default => 'fa-info-circle',
            };
          @endphp
          <div class="flash-message flash-{{ $flash['type'] }}" data-timeout="5000" role="alert">
            <span class="flash-icon"><i class="fas {{ $iconClass }}"></i></span>
            <span class="flash-text">{{ $flash['message'] }}</span>
            <button type="button" class="flash-close" aria-label="Fechar" data-flash-close>&times;</button>
          </div>
        @endforeach
      </div>
      <script>
        document.addEventListener('DOMContentLoaded', function () {
          const messages = document.querySelectorAll('.flash-message');
          const hideMessage = (element) => {
            if (!element) return;
            element.classList.add('flash-hide');
            setTimeout(() => element.remove(), 300);
          };

          messages.forEach((message) => {
            const timeout = parseInt(message.dataset.timeout, 10) || 5000;
            setTimeout(() => hideMessage(message), timeout);
          });

          document.querySelectorAll('[data-flash-close]').forEach((button) => {
            button.addEventListener('click', (event) => {
              hideMessage(event.currentTarget.closest('.flash-message'));
            });
          });
        });
      </script>
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