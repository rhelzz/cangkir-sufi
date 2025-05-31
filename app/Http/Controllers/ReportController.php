<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function salesReport(Request $request)
    {
        $period = $request->period ?? 'daily';
        $fromDate = $request->from_date ? Carbon::parse($request->from_date) : null;
        $toDate = $request->to_date ? Carbon::parse($request->to_date) : null;
        
        // Set default dates if not provided
        if (!$fromDate || !$toDate) {
            switch ($period) {
                case 'daily':
                    $fromDate = Carbon::today();
                    $toDate = Carbon::today();
                    break;
                case 'weekly':
                    $fromDate = Carbon::now()->startOfWeek();
                    $toDate = Carbon::now()->endOfWeek();
                    break;
                case 'monthly':
                    $fromDate = Carbon::now()->startOfMonth();
                    $toDate = Carbon::now()->endOfMonth();
                    break;
                case 'yearly':
                    $fromDate = Carbon::now()->startOfYear();
                    $toDate = Carbon::now()->endOfYear();
                    break;
                case 'custom':
                    if (!$fromDate) $fromDate = Carbon::now()->subDays(30);
                    if (!$toDate) $toDate = Carbon::now();
                    break;
            }
        }
        
        // Add time to dates
        $fromDateTime = $fromDate->copy()->startOfDay();
        $toDateTime = $toDate->copy()->endOfDay();
        
        // Get top selling products
        $topProducts = OrderItem::select(
            'product_id',
            DB::raw('SUM(quantity) as total_qty'),
            DB::raw('SUM(subtotal) as total_amount')
        )
            ->with('product')
            ->whereHas('order', function ($query) use ($fromDateTime, $toDateTime) {
                $query->whereBetween('created_at', [$fromDateTime, $toDateTime]);
            })
            ->groupBy('product_id')
            ->orderBy('total_qty', 'desc')
            ->take(10)
            ->get();
            
        // Get summary
        $summary = Order::whereBetween('created_at', [$fromDateTime, $toDateTime])
            ->selectRaw('
                COUNT(id) as total_orders,
                SUM(total_amount) as total_sales,
                SUM(final_amount) as final_sales,
                AVG(final_amount) as avg_sale
            ')
            ->first();
            
        return view('reports.sales', compact(
            'period',
            'fromDate',
            'toDate',
            'topProducts',
            'summary'
        ));
    }
    
    public function busyHoursReport(Request $request)
    {
        $period = $request->period ?? 'daily';
        $fromDate = $request->from_date ? Carbon::parse($request->from_date) : null;
        $toDate = $request->to_date ? Carbon::parse($request->to_date) : null;

        // Set default dates if not provided
        if (!$fromDate || !$toDate) {
            switch ($period) {
                case 'daily':
                    $fromDate = Carbon::today();
                    $toDate = Carbon::today();
                    break;
                case 'weekly':
                    $fromDate = Carbon::now()->startOfWeek();
                    $toDate = Carbon::now()->endOfWeek();
                    break;
                case 'monthly':
                    $fromDate = Carbon::now()->startOfMonth();
                    $toDate = Carbon::now()->endOfMonth();
                    break;
                case 'yearly':
                    $fromDate = Carbon::now()->startOfYear();
                    $toDate = Carbon::now()->endOfYear();
                    break;
                case 'custom':
                    if (!$fromDate) $fromDate = Carbon::now()->subDays(30);
                    if (!$toDate) $toDate = Carbon::now();
                    break;
            }
        }

        // Add time to dates
        $fromDateTime = $fromDate->copy()->startOfDay();
        $toDateTime = $toDate->copy()->endOfDay();

        // Get orders by hour (all 24 hours)
        $hourlyData = Order::whereBetween('created_at', [$fromDateTime, $toDateTime])
            ->selectRaw('HOUR(created_at) as hour, COUNT(id) as total_orders, SUM(final_amount) as total_sales')
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->get();

        $hourLabels = [];
        $hourValues = [];
        $hourSales = [];
        $hourList = [];

        for ($i = 0; $i < 24; $i++) {
            $hour = sprintf('%02d:00', $i);
            $hourLabels[] = $hour;

            $found = $hourlyData->firstWhere('hour', $i);
            $orders = $found ? $found->total_orders : 0;
            $sales = $found ? $found->total_sales : 0;

            $hourValues[] = $orders;
            $hourSales[] = $sales;
            $hourList[] = [
                'label' => $hour,
                'orders' => $orders,
                'sales' => $sales
            ];
        }

        // Sort hours by orders descending for top 5 list
        $top5Hours = collect($hourList)->sortByDesc('orders')->take(5)->values();

        // Top 1 hour
        $top1Hour = $top5Hours->first();

        // Get orders by day of week (for period > daily)
        $dailyData = [];
        $dayLabels = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        $dayValues = [0, 0, 0, 0, 0, 0, 0];
        $daySales = [0, 0, 0, 0, 0, 0, 0];
        $dayList = [];

        if ($period !== 'daily') {
            $dailyData = Order::whereBetween('created_at', [$fromDateTime, $toDateTime])
                ->selectRaw('DAYOFWEEK(created_at) as day, COUNT(id) as total_orders, SUM(final_amount) as total_sales')
                ->groupBy(DB::raw('DAYOFWEEK(created_at)'))
                ->get();

            foreach ($dailyData as $data) {
                $index = $data->day - 1; // DAYOFWEEK: 1=Sun, 7=Sat
                $dayValues[$index] = $data->total_orders;
                $daySales[$index] = $data->total_sales;
            }

            foreach ($dayLabels as $i => $label) {
                $dayList[] = [
                    'label' => $label,
                    'orders' => $dayValues[$i],
                    'sales' => $daySales[$i]
                ];
            }
        }

        // Sort days by orders descending for top 5 list
        $top5Days = collect($dayList)->sortByDesc('orders')->take(5)->values();
        // Top 1 day
        $top1Day = $top5Days->first();

        return view('reports.busy-hours', compact(
            'period',
            'fromDate',
            'toDate',
            'hourLabels',
            'hourValues',
            'hourSales',
            'top5Hours',
            'top1Hour',
            'dayLabels',
            'dayValues',
            'daySales',
            'top5Days',
            'top1Day'
        ));
    }
}
