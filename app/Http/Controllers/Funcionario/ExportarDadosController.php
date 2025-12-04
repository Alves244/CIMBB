<?php

namespace App\Http\Controllers\Funcionario;

use App\Http\Controllers\Controller;
use App\Models\Familia;
use App\Models\InqueritoFreguesia;
use App\Models\TicketSuporte;
use App\Services\RegionalDashboardService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExportarDadosController extends Controller
{
    public function __construct(private RegionalDashboardService $dashboardService)
    {
        $this->middleware(['auth', 'check_funcionario']);
    }

    public function index()
    {
        $anos = collect(range(date('Y'), date('Y') - 5));

        return view('funcionario.exportar.index', [
            'title' => 'Exportar Dados',
            'anosDisponiveis' => $anos,
        ]);
    }

    public function exportCsv(Request $request)
    {
        $dataset = $request->input('dataset', 'familias');
        $filename = sprintf('%s_%s.csv', $dataset, now()->format('Ymd_His'));

        $callback = function () use ($dataset) {
            $handle = fopen('php://output', 'w');

            switch ($dataset) {
                case 'inqueritos':
                    fputcsv($handle, ['Concelho', 'Freguesia', 'Ano', 'Adultos', 'Crianças', 'Escala integração']);
                    InqueritoFreguesia::with('freguesia.conselho')
                        ->orderBy('ano', 'desc')
                        ->chunk(200, function ($inqueritos) use ($handle) {
                            foreach ($inqueritos as $inquerito) {
                                fputcsv($handle, [
                                    optional(optional($inquerito->freguesia)->conselho)->nome,
                                    optional($inquerito->freguesia)->nome,
                                    $inquerito->ano,
                                    $inquerito->total_adultos,
                                    $inquerito->total_criancas,
                                    $inquerito->escala_integracao,
                                ]);
                            }
                        });
                    break;

                case 'tickets':
                    fputcsv($handle, ['Código', 'Assunto', 'Estado', 'Utilizador', 'Criado em', 'Respondido em']);
                    TicketSuporte::with('utilizador')
                        ->orderBy('created_at', 'desc')
                        ->chunk(200, function ($tickets) use ($handle) {
                            foreach ($tickets as $ticket) {
                                fputcsv($handle, [
                                    $ticket->codigo,
                                    $ticket->assunto,
                                    $ticket->estado,
                                    optional($ticket->utilizador)->email,
                                    optional($ticket->created_at)?->format('Y-m-d H:i'),
                                    optional($ticket->data_resposta)?->format('Y-m-d H:i'),
                                ]);
                            }
                        });
                    break;

                case 'familias':
                default:
                    fputcsv($handle, ['Código', 'Concelho', 'Freguesia', 'Nacionalidade', 'Membros', 'Criado em']);
                    Familia::with(['freguesia.conselho', 'agregadoFamiliar'])
                        ->orderBy('id')
                        ->chunk(200, function ($familias) use ($handle) {
                            foreach ($familias as $familia) {
                                fputcsv($handle, [
                                    $familia->codigo,
                                    optional(optional($familia->freguesia)->conselho)->nome,
                                    optional($familia->freguesia)->nome,
                                    $familia->nacionalidade,
                                    optional($familia->agregadoFamiliar)->total_membros,
                                    optional($familia->created_at)?->format('Y-m-d'),
                                ]);
                            }
                        });
                    break;
            }

            fclose($handle);
        };

        return response()->streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function exportInqueritosPdf(Request $request)
    {
        $ano = (int) $request->input('ano', date('Y'));
        $inqueritos = InqueritoFreguesia::with('freguesia.conselho')
            ->where('ano', $ano)
            ->orderBy('freguesia_id')
            ->get();

        $pdf = Pdf::loadView('funcionario.exportar.pdf_inqueritos', [
            'ano' => $ano,
            'inqueritos' => $inqueritos,
        ])->setPaper('a4', 'portrait');

        return $pdf->download(sprintf('inqueritos_%d.pdf', $ano));
    }

    public function exportEstatisticasPdf(Request $request)
    {
        $ano = (int) $request->input('ano', date('Y'));
        $overview = $this->dashboardService->getRegionalOverview($ano);

        $pdf = Pdf::loadView('funcionario.exportar.pdf_estatisticas', [
            'ano' => $ano,
            'concelhosResumo' => $overview['concelhosResumo'],
            'dashboardProgress' => $overview['dashboardProgress'],
        ])->setPaper('a4', 'landscape');

        return $pdf->download(sprintf('estatisticas_%d.pdf', $ano));
    }
}
