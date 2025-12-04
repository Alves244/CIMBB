<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="utf-8">
  <title>Estatísticas CIMBB - {{ $ano }}</title>
  <style>
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #222; }
    h1 { font-size: 20px; margin-bottom: 4px; }
    h2 { font-size: 16px; margin-top: 24px; margin-bottom: 8px; }
    p { margin: 0 0 6px 0; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
    th, td { border: 1px solid #ddd; padding: 6px 8px; }
    th { background: #f1f5f9; text-transform: uppercase; font-size: 11px; letter-spacing: .5px; }
    .muted { color: #555; font-size: 11px; }
    .tag { display: inline-block; border: 1px solid #cbd5f5; padding: 2px 6px; border-radius: 4px; font-size: 10px; text-transform: uppercase; margin-right: 4px; }
    .grid { display: flex; gap: 12px; }
    .grid .card { flex: 1; border: 1px solid #e5e7eb; border-radius: 6px; padding: 8px 10px; }
    .card strong { font-size: 18px; display: block; }
  </style>
</head>
<body>
  <h1>Estatísticas CIMBB · {{ $ano }}</h1>
  <p class="muted">Gerado em {{ $geradoEm->format('d/m/Y H:i') }}</p>

  <div class="grid">
    <div class="card">
      <span class="muted">Famílias no filtro</span>
      <strong>{{ $totais['totalFamilias'] }}</strong>
    </div>
    <div class="card">
      <span class="muted">Total de membros</span>
      <strong>{{ $totais['totalMembros'] }}</strong>
      <span class="muted">{{ $totais['adultosLaboral'] }} em idade ativa</span>
    </div>
    <div class="card">
      <span class="muted">Crianças · 65+</span>
      <strong>{{ $totais['criancas'] }} / {{ $totais['adultosSenior'] }}</strong>
    </div>
    <div class="card">
      <span class="muted">Nacionalidades distintas</span>
      <strong>{{ $totais['totalNacionalidades'] }}</strong>
    </div>
  </div>

  <h2>Filtros aplicados</h2>
  <div>
    @foreach($filtros as $chave => $valor)
      @continue($chave === 'freguesias_submetidas')
      @continue(in_array($chave, ['periodo_inicio', 'periodo_fim']) && !$valor)
      <span class="tag">{{ \Illuminate\Support\Str::headline(str_replace('_', ' ', $chave)) }}: {{ is_array($valor) ? json_encode($valor) : ($valor instanceof \Carbon\Carbon ? $valor->format('d/m/Y') : ($valor ?: 'Todos')) }}</span>
    @endforeach
  </div>

  <h2>Famílias listadas</h2>
  <table>
    <thead>
      <tr>
        <th>Família</th>
        <th>Concelho</th>
        <th>Freguesia</th>
        <th>Habitação</th>
        <th>Propriedade</th>
        <th>Nacionalidade</th>
        <th>Membros</th>
        <th>Inquérito</th>
      </tr>
    </thead>
    <tbody>
      @forelse($listaFamilias as $familia)
        <tr>
          <td>{{ $familia['codigo'] }}</td>
          <td>{{ $familia['concelho'] }}</td>
          <td>{{ $familia['freguesia'] }}</td>
          <td>{{ ucfirst($familia['tipologia_habitacao']) }}</td>
          <td>{{ ucfirst($familia['tipologia_propriedade']) }}</td>
          <td>{{ $familia['nacionalidade'] }}</td>
          <td>{{ $familia['total_membros'] }}</td>
          <td>{{ $familia['situacao_inquerito'] }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8">Sem famílias para os filtros selecionados.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <h2>Status dos inquéritos</h2>
  <p>Total de freguesias consideradas: {{ $freguesiasResumo['totalConsideradas'] }} · Com inquérito: {{ $freguesiasResumo['comInquerito'] }}</p>
  @if($freguesiasResumo['pendentes']->isNotEmpty())
    <table>
      <thead>
        <tr>
          <th>Freguesia pendente</th>
          <th>Código</th>
        </tr>
      </thead>
      <tbody>
        @foreach($freguesiasResumo['pendentes'] as $pendente)
          <tr>
            <td>{{ $pendente->nome }}</td>
            <td>{{ $pendente->codigo }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  @else
    <p class="muted">Não existem freguesias pendentes para este recorte.</p>
  @endif
</body>
</html>
