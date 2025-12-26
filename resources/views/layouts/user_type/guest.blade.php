@extends('layouts.app')

@section('guest')
    <div style="min-height: 100vh; display: flex; flex-direction: column;">
        <div style="flex: 1 0 auto;">
            @yield('content')
        </div>
        <div style="flex-shrink: 0;">
            {{-- E o rodapé --}}
            @include('layouts.footers.guest.footer')
        </div>
    </div>
@endsection