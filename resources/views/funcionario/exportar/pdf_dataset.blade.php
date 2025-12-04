<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <title>{{ $titulo }}</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #1f2937; }
    h1 { font-size: 20px; margin-bottom: 4px; }
    p { margin: 0 0 6px 0; }
    table { width: 100%; border-collapse: collapse; margin-top: 12px; }
    th, td { border: 1px solid #d1d5db; padding: 6px 8px; }
    th { background: #f3f4f6; text-transform: uppercase; letter-spacing: .4px; font-size: 11px; }
    .muted { color: #6b7280; font-size: 11px; }
  </style>
</head>
<body>
  <h1>{{ $titulo }}</h1>
  <p class="muted">Gerado em {{ $geradoEm->format('d/m/Y H:i') }}</p>

  <table>
    <thead>
      <tr>
        @foreach($colunas as $coluna)
          <th>{{ $coluna }}</th>
        @endforeach
      </tr>
    </thead>
    <tbody>
      @forelse($registos as $linha)
        <tr>
          @foreach($linha as $valor)
            <td>{{ $valor }}</td>
          @endforeach
        </tr>
      @empty
        <tr>
          <td colspan="{{ count($colunas) }}">Sem dados para exportar.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
</body>
</html>
