<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Filament\Actions\Action;
use Carbon\Carbon;

class MonthlySalesChart extends ChartWidget
{
    protected static ?string $heading = 'Monthly Sales (Toman)';
    protected static ?int $sort = 1; // Determines position on dashboard

    protected function getData(): array
    {
        // Fetch all non-cancelled orders
        $orders = Order::where('status', '!=', 'cancelled')->get();

        // Group by month (YYYY-MM) and sum the totals
        $monthlySales = $orders->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('Y-m');
        })->map(function ($group) {
            return $group->sum('total');
        })->sortKeys(); // Sort by month

        // Prepare data for Chart.js
        $labels = $monthlySales->keys()->map(function ($month) {
            return Carbon::parse($month . '-01')->format('M Y'); // e.g., "Oct 2026"
        })->toArray();

        $data = $monthlySales->values()->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Sales',
                    'data' => $data,
                    'backgroundColor' => '#682535', // Your brand color
                    'borderColor' => '#682535',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Can be 'bar', 'line', 'pie', etc.
    }

    // Add a Print Button
    protected function getHeaderActions(): array
    {
        return [
            Action::make('print')
                ->label('Print Chart')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url('javascript:window.print()')
                ->openUrlInNewTab(false),
        ];
    }
}