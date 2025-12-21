<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <title>Estatísticas Escolas CIMBB - {{ $ano }}</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #1f2933; }
    h1 { font-size: 20px; margin-bottom: 4px; }
    h2 { font-size: 16px; margin-top: 24px; margin-bottom: 8px; }
    p { margin: 0 0 6px 0; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    th, td { border: 1px solid #d1d5db; padding: 6px 8px; }
    th { background: #f3f4f6; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; }
    .muted { color: #637381; font-size: 11px; }
    .grid { display: flex; gap: 12px; }
    .grid .card { flex: 1; border: 1px solid #d1d5db; border-radius: 6px; padding: 8px 10px; }
    .card strong { font-size: 18px; display: block; }
    .tag { display: inline-block; border: 1px solid #cbd5f5; padding: 2px 6px; border-radius: 4px; font-size: 10px; text-transform: uppercase; margin-right: 4px; margin-bottom: 4px; }
  </style>
</head>
<body>
  @php
    $listaInqueritos = collect($listaInqueritos);
  @endphp

  <h1>Estatísticas das Escolas · {{ $ano }}</h1>
  <p class="muted">Gerado em {{ $geradoEm->format('d/m/Y H:i') }}</p>

  <div class="grid">
    <div class="card">
      <span class="muted">Inquéritos submetidos</span>
      <strong>{{ $totais['totalInqueritos'] }}</strong>
    </div>
    <div class="card">
      <span class="muted">Agrupamentos abrangidos</span>
      <strong>{{ $totais['totalAgrupamentos'] }}</strong>
    </div>
    <div class="card">
      <span class="muted">Alunos reportados</span>
      <strong>{{ $totais['totalAlunos'] }}</strong>
      <span class="muted">Média por inquérito: {{ $totais['mediaAlunos'] }}</span>
    </div>
  </div>

  <h2>Filtros aplicados</h2>
  <div>
    @foreach($filtros as $chave => $valor)
      <span class="tag">{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $chave)) }}: {{ ($valor === null || $valor === '') ? 'Todos' : $valor }}</span>
    @endforeach
  </div>

  <h2>Totais por nacionalidade</h2>
  @if(!empty($distribuicoes['nacionalidade']))
    <table>
      <thead>
        <tr>
          <th>Nacionalidade</th>
          <th>Alunos</th>
        </tr>
      </thead>
      <tbody>
        @foreach($distribuicoes['nacionalidade'] as $nacionalidade => $total)
          <tr>
            <td>{{ $nacionalidade }}</td>
            <td>{{ $total }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <p class="muted">Sem registos para os filtros selecionados.</p>
  @endif

  <h2>Totais por nível de ensino</h2>
  @if(!empty($distribuicoes['nivel_ensino']))
    <table>
      <thead>
        <tr>
          <th>Nível de ensino</th>
          <th>Alunos</th>
        </tr>
      </thead>
      <tbody>
        @foreach($distribuicoes['nivel_ensino'] as $nivel => $total)
          <tr>
            <td>{{ ucfirst(str_replace('_', ' ', $nivel ?: 'Indefinido')) }}</td>
            <td>{{ $total }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <p class="muted">Sem registos para os filtros selecionados.</p>
  @endif

  <h2>Totais por estabelecimento</h2>
  @if(!empty($distribuicoes['agrupamentos']))
    <table>
      <thead>
        <tr>
          <th>Agrupamento</th>
          <th>Alunos</th>
        </tr>
      </thead>
      <tbody>
        @foreach($distribuicoes['agrupamentos'] as $agrupamento => $total)
          <tr>
            <td>{{ $agrupamento }}</td>
            <td>{{ $total }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <p class="muted">Sem registos para os filtros selecionados.</p>
  @endif

  <h2>Totais por concelho</h2>
  @if(!empty($distribuicoes['concelho']))
    <table>
      <thead>
        <tr>
          <th>Concelho</th>
          <th>Alunos</th>
        </tr>
      </thead>
      <tbody>
        @foreach($distribuicoes['concelho'] as $concelho => $total)
          <tr>
            <td>{{ $concelho }}</td>
            <td>{{ $total }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <p class="muted">Sem registos para os filtros selecionados.</p>
  @endif

  <h2>Inquéritos submetidos</h2>
  @if($listaInqueritos->isNotEmpty())
    <table>
      <thead>
        <tr>
          <th>Agrupamento</th>
          <th>Concelho</th>
          <th>Ano</th>
          <th>Alunos reportados</th>
        </tr>
      </thead>
      <tbody>
        @foreach($listaInqueritos as $inquerito)
          <tr>
            <td>{{ optional($inquerito->agrupamento)->nome ?? '—' }}</td>
            <td>{{ optional(optional($inquerito->agrupamento)->concelho)->nome ?? '—' }}</td>
            <td>{{ $inquerito->ano_referencia }}</td>
            <td>{{ $inquerito->alunos_reportados ?? 0 }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <p class="muted">Nenhum inquérito encontrado para os filtros selecionados.</p>
  @endif
</body>
</html>
