@component('layouts.email')
    <h2 style="color: #111827; font-size: 20px;">Olá {{ $user->nome }},</h2>

    <p>Foi criado um acesso para si na plataforma <strong>CIMBB</strong>. Utilize as credenciais abaixo para iniciar sessão:</p>

    <div style="background-color: #f9fafb; padding: 20px; border-radius: 6px; border: 1px solid #edf2f7; margin: 20px 0;">
        <p style="margin: 0; font-size: 15px;"><strong>E-mail:</strong> {{ $user->email }}</p>
        <p style="margin: 10px 0 0 0; font-size: 15px;"><strong>Password provisória:</strong> <code style="background: #e2e8f0; padding: 2px 6px; border-radius: 4px;">{{ $password }}</code></p>
    </div>

    <p>Pode entrar através do botão abaixo:</p>
    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $loginUrl }}" style="background-color: #1a5632; color: #ffffff; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
            Iniciar Sessão
        </a>
    </div>

    <p style="font-size: 14px; color: #718096; font-style: italic;">
        Por motivos de segurança, recomendamos que altere a password após o primeiro acesso através da área "Perfil".
    </p>

    <p style="margin-top: 25px;">
        Bem-vindo e bom trabalho!<br>
        <strong>Equipa CIMBB</strong>
    </p>
@endcomponent