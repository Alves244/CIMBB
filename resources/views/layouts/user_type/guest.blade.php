@extends('layouts.app')

@section('guest')
    
    @yield('content')
    
    {{-- E o rodapé --}}
    @include('layouts.footers.guest.footer')

@endsection