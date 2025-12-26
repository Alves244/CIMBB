@extends('layouts.user_type.guest')

@section('content')

  <main class="main-content mt-0">
    <section class="min-vh-100 d-flex align-items-start pt-4 pt-md-5 pb-4">
      <div class="container-fluid px-0">
        <div class="row justify-content-between align-items-center gy-5 px-4">
          <div class="col-xl-4 col-lg-5 col-md-6 d-flex flex-column me-auto" style="margin-left:-30px;">
            <div class="card card-plain mt-md-5">
              <div class="card-header pb-0 text-left bg-transparent">
                <h2 class="fs-2 fw-bold text-success text-gradient mb-2">Bem-vindo de volta</h2>
                <p class="mb-0">Insira as suas credenciais para aceder.</p>
              </div>
              <div class="card-body">
                <form role="form" method="POST" action="/session">
                  @csrf
                  <label>Email</label>
                  <div class="mb-3">
                    <input type="email" class="form-control" name="email" id="email" placeholder="Email" aria-label="Email" aria-describedby="email-addon">
                    @error('email')
                      <p class="text-danger text-xs mt-2">{{ $message }}</p>
                    @enderror
                  </div>
                  <label>Password</label>
                  <div class="mb-3">
                    <input type="password" class="form-control" name="password" id="password" placeholder="Password" aria-label="Password" aria-describedby="password-addon">
                    @error('password')
                      <p class="text-danger text-xs mt-2">{{ $message }}</p>
                    @enderror
                  </div>
                  <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="rememberMe" checked="">
                    <label class="form-check-label" for="rememberMe">Lembrar-me</label>
                  </div>
                  <div class="text-center">
                    <button type="submit" class="btn bg-gradient-success w-100 mt-4 mb-0">Entrar</button>
                  </div>
                </form>
              </div>
              <div class="card-footer text-center pt-0 px-lg-2 px-1">
                <small class="text-muted">Esqueceu-se da password? Redefina a sua password
                  <a href="/forgot-password" class="text-success text-gradient font-weight-bold">aqui</a>
                </small>
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