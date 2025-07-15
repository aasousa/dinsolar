<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Residence;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ConsumptionChart extends ChartWidget
{
    protected static ?string $heading = 'Consumo mensal';
    protected static ?int $sort = 1;

    protected function getFilters(): ?array
    {
        return Residence::query()
            ->when(
                !auth()->user()->hasRole('admin'),
                fn($query) => $query->where('user_id', auth()->id())
            )
            ->pluck('label', 'id') // substitua 'nome' por outro campo se necessário
            ->toArray();
    }

    protected function getData(): array
    {
        $residenceId = $residenceId = $this->filter ?? Residence::query()
            ->when(
                !auth()->user()->hasRole('admin'),
                fn($query) => $query->where('user_id', auth()->id())
            )
            ->value('id');


        $dados = DB::table('consuptions')
            ->selectRaw('MONTH(date) as mes, SUM(kwh) as total')
            ->where('residence_id', $residenceId)
            ->whereYear('date', now()->year)
            ->groupBy('mes')
            ->orderBy('mes')
            ->pluck('total', 'mes');

        $labels = collect(range(1, 12))->map(fn($mes) => Carbon::create()->month($mes)->format('M'))->toArray();
        $valores = collect(range(1, 12))->map(fn($mes) => $dados[$mes] ?? 0)->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'kWh',
                    'data' => $valores,
                    'backgroundColor' => '#3b82f6',
                ],
            ],
            'labels' => $labels,
        ];
    }


    protected function getType(): string
    {
        return 'bar';
    }

    protected function getDefaultFilter(): ?string
    {
        return Residence::query()
            ->when(
                !auth()->user()->hasRole('admin'),
                fn($query) => $query->where('user_id', auth()->id())
            )
            ->value('id');
    }
}
