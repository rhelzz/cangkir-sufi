<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Expense;
use App\Models\Product;
use App\Models\ExpenseItem;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Today's sales
        $todayStart = Carbon::today();
        $todayEnd = Carbon::today()->endOfDay();
        
        $todaySales = Order::whereBetween('created_at', [$todayStart, $todayEnd])
            ->sum('final_amount');
            
        // Calculate today's expenses by summing up the prices of all expense items for expenses from today
        $todayExpenses = ExpenseItem::whereHas('expense', function($query) use ($todayStart, $todayEnd) {
            $query->whereBetween('expense_date', [$todayStart, $todayEnd]);
        })->sum('price');
            
        $todayProfit = $todaySales - $todayExpenses;
        
        // Transaction count today
        $todayTransactions = Order::whereBetween('created_at', [$todayStart, $todayEnd])
            ->count();
            
        // Last 5 transactions
        $latestTransactions = Order::with(['user', 'orderItems.product'])
            ->latest()
            ->take(5)
            ->get();
            
        // Low stock products (less than 10 items)
        $lowStockProducts = Product::where('stock', '<', 10)
            ->orderBy('stock')
            ->take(5)
            ->get();
            
        return view('dashboard', compact(
            'todaySales', 
            'todayExpenses', 
            'todayProfit', 
            'todayTransactions', 
            'latestTransactions', 
            'lowStockProducts'
        ));
    }
    
    public function getChartData()
    {
        // Last 7 days sales data for chart
        $salesData = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('D');
            
            $sales = Order::whereDate('created_at', $date)->sum('final_amount');
            $salesData[] = $sales;
        }
        
        return response()->json([
            'labels' => $labels,
            'salesData' => $salesData,
        ]);
    }
}