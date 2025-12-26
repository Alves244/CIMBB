<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

// Controlador base que centraliza as funcionalidades essenciais para o portal web
class Controller extends BaseController
{
    // Traits que fornecem validação e autorização necessárias para a segurança do sistema 
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}