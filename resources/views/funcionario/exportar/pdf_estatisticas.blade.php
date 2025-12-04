<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Estatísticas Regionais {{ $ano }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
        h1 { font-size: 20px; margin-bottom: 5px; }
        p { margin: 0 0 10px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #bbb; padding: 5px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Estatísticas Regionais {{ $ano }}</h1>
    <p>Concelhos com inquérito concluído: {{ $dashboardProgress['concelhosComInquerito'] }} de {{ $dashboardProgress['totalConcelhos'] }} ({{ $dashboardProgress['percentual'] }}%).</p>
    <table>
        <thead>
            <tr>
                <th>Concelho</th>
                <th>Código</th>
                <th>Famílias</th>
                <th>Membros</th>
                <th>Tickets pendentes</th>
                <th>Freguesias com inquérito</th>
                <th>Total de freguesias</th>
                <th>% Inquérito</th>
            </tr>
        </thead>
        <tbody>
            @forelse($concelhosResumo as $linha)
                <tr>
                    <td>{{ $linha['nome'] }}</td>
                    <td>{{ $linha['codigo'] }}</td>
                    <td>{{ $linha['total_familias'] }}</td>
                    <td>{{ $linha['total_membros'] }}</td>
                    <td>{{ $linha['tickets_pendentes'] }}</td>
                    <td>{{ $linha['freguesias_com_inquerito'] }}</td>
                    <td>{{ $linha['total_freguesias'] }}</td>
                    <td>{{ $linha['percentual_inquerito'] }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8">Sem registos para o ano selecionado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
