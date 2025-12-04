<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Relatório de Inquéritos {{ $ano }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { font-size: 20px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h1>Relatório de Inquéritos {{ $ano }}</h1>
    <p>Total de registos: {{ $inqueritos->count() }}</p>
    <table>
        <thead>
            <tr>
                <th>Concelho</th>
                <th>Freguesia</th>
                <th>Adultos</th>
                <th>Crianças</th>
                <th>Escala Integração</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inqueritos as $inquerito)
                <tr>
                    <td>{{ optional(optional($inquerito->freguesia)->conselho)->nome }}</td>
                    <td>{{ optional($inquerito->freguesia)->nome }}</td>
                    <td>{{ $inquerito->total_adultos }}</td>
                    <td>{{ $inquerito->total_criancas }}</td>
                    <td>{{ $inquerito->escala_integracao }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Sem registos para o ano selecionado.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
