@extends('layouts.user_type.guest')

@section('content')


<main class="main-content mt-0">
    <section class="d-flex align-items-center justify-content-center" style="padding-top: 40px; padding-bottom: 0;">
        <div class="container-fluid px-0">
            <div class="row justify-content-between align-items-center gy-5 px-4">
                <div class="col-xl-4 col-lg-5 col-md-6 d-flex flex-column me-auto" style="margin-left:-30px;">
                    <div class="card card-plain mt-4 mb-2">
                        <div class="card-header pb-0 text-left bg-transparent">
                              <h3 class="fs-3 fw-bold text-success text-gradient mb-2">Recuperar password</h3>
                            <p class="mb-0">Insira o seu e-mail para receber o link de redefinição.</p>
                        </div>
                        <div class="card-body">
                            <form action="/forgot-password" method="POST" role="form text-left">
                                @csrf
                                <label for="email">E-mail</label>
                                <div class="mb-3">
                                    <input id="email" name="email" type="email" class="form-control" placeholder="O seu e-mail" aria-label="Email" aria-describedby="email-addon">
                                    @error('email')
                                        <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="text-center">
                                    <button type="submit" class="btn bg-gradient-success w-100 mt-4 mb-0 fw-bold text-white">RECEBER LINK DE RECUPERAÇÃO</button>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer text-center pt-0 px-lg-2 px-1">
                            <small class="text-muted">Lembrou-se da password? <a href="/login" class="text-success text-gradient font-weight-bold">Entrar</a></small>
                        </div>
                    </div>
                </div>
                <div class="col-xl-6 col-lg-6 col-md-8 d-flex justify-content-start ms-xl-n4">
                    <div class="w-100 d-flex justify-content-start align-items-center">
                        <img src="{{ asset('assets/img/cimbb/logo/logo-cimbb.png') }}"
                                 class="img-fluid"
                                 style="max-width: 720px; width: 100%;"
                                 alt="Logotipo CIMBB">
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

@endsection