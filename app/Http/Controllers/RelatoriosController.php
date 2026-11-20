<?php

namespace App\Http\Controllers;

use App\Models\Compras;
use App\Models\ItensCompra;
use App\Models\Produtos;
use App\Models\Pagamentos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RelatoriosController extends Controller
{
    public function index(Request $request)
    {
        $periodo = $request->query('periodo', 'dia');
        $fim = now();

        // 1. Definição do intervalo de datas e formato do gráfico
        switch ($periodo) {
            case 'semana':
                $inicio = now()->subDays(7)->startOfDay();
                $groupFormat = '%d/%m'; // Ex: 15/10
                break;
            case 'mes':
                $inicio = now()->startOfMonth();
                $groupFormat = '%d/%m'; // Ex: 15/10
                break;
            case 'ano':
                $inicio = now()->startOfYear();
                $groupFormat = '%m/%Y'; // Ex: 10/2023
                break;
            case 'dia':
            default:
                $inicio = now()->startOfDay();
                $groupFormat = '%H:00'; // Agrupa por hora no dia de hoje (Ex: 14:00)
                break;
        }

        // 2. Faturamento e KPIs globais do período
        $faturamentoTotal = Compras::whereBetween('data_inicio', [$inicio, $fim])
            ->where('status', 'Concluida')
            ->sum('valor_total');

        $totalCompras = Compras::whereBetween('data_inicio', [$inicio, $fim])
            ->where('status', 'Concluida')
            ->count();

        $ticketMedio = $totalCompras > 0 ? $faturamentoTotal / $totalCompras : 0;

        // 3. Top 5 produtos mais vendidos
        $topProdutos = ItensCompra::select('id_produto', DB::raw('SUM(quantidade) as total_vendido'))
            ->whereHas('compra', function ($q) use ($inicio, $fim) {
                $q->whereBetween('data_inicio', [$inicio, $fim])
                  ->where('status', 'Concluida');
            })
            ->groupBy('id_produto')
            ->orderByDesc('total_vendido')
            ->limit(5)
            ->with('produto') // Traz a relação do nome do produto
            ->get();

        // 4. Distribuição de Formas de Pagamento (PIX, Cartão, etc)
        $pagamentoDist = Pagamentos::select('metodo', DB::raw('COUNT(*) as total'))
            ->whereHas('compra', function ($q) use ($inicio, $fim) {
                $q->whereBetween('data_inicio', [$inicio, $fim])
                  ->where('status', 'Concluida');
            })
            ->groupBy('metodo')
            ->pluck('total', 'metodo')
            ->toArray();

        // 5. Histórico do Gráfico Linear (Agrupado por hora, dia ou mês dependendo do filtro)
        $faturamentoTempo = Compras::select(
                DB::raw("DATE_FORMAT(data_inicio, '$groupFormat') as periodo"),
                DB::raw('SUM(valor_total) as total')
            )
            ->whereBetween('data_inicio', [$inicio, $fim])
            ->where('status', 'Concluida')
            ->groupBy('periodo')
            ->orderByRaw('MIN(data_inicio) ASC')
            ->get();

        // 6. Últimas transações detalhadas para a tabela inferior
        $ultimasCompras = Compras::with(['usuario', 'stroller'])
            ->whereBetween('data_inicio', [$inicio, $fim])
            ->orderByDesc('data_inicio')
            ->limit(10)
            ->get();

        return view('admin.relatorios', compact(
            'periodo',
            'faturamentoTotal',
            'ticketMedio',
            'totalCompras',
            'topProdutos',
            'pagamentoDist',
            'faturamentoTempo',
            'ultimasCompras'
        ));
    }

    public function export(Request $request)
    {
        // Retorna um JSON simples por enquanto, podendo evoluir para CSV ou PDF posteriormente
        $response = $this->index($request);
        return response()->json($response->getData());
    }
}