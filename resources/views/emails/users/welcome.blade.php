<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Bem-vindo</title>
</head>
<body style="font-family: Arial, sans-serif; color: #111827; line-height: 1.6;">
    <p>Olá {{ $user->nome }},</p>

    <p>Foi criado um acesso para si na plataforma CIMBB. Utilize as credenciais abaixo para iniciar sessão e completar o seu perfil.</p>

    <p>
        <strong>Email:</strong> {{ $user->email }}<br>
        <strong>Password provisória:</strong> {{ $password }}
    </p>

    <p>
        Pode entrar através do seguinte endereço:
        <a href="{{ $loginUrl }}" style="color: #0d6efd;">{{ $loginUrl }}</a>
    </p>

    <p>Por motivos de segurança, recomendamos que altere a password após o primeiro acesso, através da área "Perfil".</p>

    <p>Bem-vindo e bom trabalho!</p>

    <p style="margin-top: 32px;">Equipa CIMBB</p>
</body>
</html>
