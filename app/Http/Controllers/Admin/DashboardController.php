<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        $ordersInProgress = Order::query()->inProgress()->count();

        $validatedToday = Order::query()
            ->whereIn('status', [Order::STATUS_READY, Order::STATUS_PAID])
            ->whereDate('updated_at', $today)
            ->count();

        $dailyRevenue = Payment::query()
            ->whereDate('paid_at', $today)
            ->sum('amount');

        $months = collect(range(0, 11))
            ->map(fn (int $offset): string => now()->subMonths(11 - $offset)->format('Y-m'));

        $ordersByMonthRaw = Order::query()
            ->selectRaw($this->monthExpression('placed_at').' as period, COUNT(*) as total')
            ->where('placed_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('period')
            ->pluck('total', 'period');

        $ordersByMonth = [
            'labels' => $months->all(),
            'values' => $months->map(fn (string $month): int => (int) ($ordersByMonthRaw[$month] ?? 0))->all(),
        ];

        $soldByCategoryRows = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('burgers', 'burgers.id', '=', 'order_items.burger_id')
            ->join('categories', 'categories.id', '=', 'burgers.category_id')
            ->whereIn('orders.status', [Order::STATUS_READY, Order::STATUS_PAID])
            ->where('orders.placed_at', '>=', now()->subMonths(11)->startOfMonth())
            ->selectRaw($this->monthExpression('orders.placed_at').' as period, categories.name as category, SUM(order_items.quantity) as total')
            ->groupBy('period', 'categories.name')
            ->orderBy('period')
            ->get();

        $categorySalesChart = $this->buildCategoryChart($months, $soldByCategoryRows);

        return view('admin.dashboard.index', [
            'ordersInProgress' => $ordersInProgress,
            'validatedToday' => $validatedToday,
            'dailyRevenue' => $dailyRevenue,
            'ordersByMonth' => $ordersByMonth,
            'categorySalesChart' => $categorySalesChart,
        ]);
    }

    private function monthExpression(string $column): string
    {
        return DB::getDriverName() === 'sqlite'
            ? "strftime('%Y-%m', {$column})"
            : "DATE_FORMAT({$column}, '%Y-%m')";
    }

    private function buildCategoryChart(Collection $months, Collection $rows): array
    {
        $palette = [
            '#f97316', '#22c55e', '#0ea5e9', '#eab308', '#ec4899', '#6366f1', '#14b8a6', '#ef4444',
        ];

        $categories = $rows->pluck('category')->unique()->values();
        $datasets = [];

        foreach ($categories as $index => $category) {
            $values = [];

            foreach ($months as $month) {
                $value = $rows
                    ->first(fn ($row) => $row->period === $month && $row->category === $category)
                    ?->total;

                $values[] = (int) ($value ?? 0);
            }

            $datasets[] = [
                'label' => $category,
                'backgroundColor' => $palette[$index % count($palette)],
                'data' => $values,
            ];
        }

        return [
            'labels' => $months->all(),
            'datasets' => $datasets,
        ];
    }
}
