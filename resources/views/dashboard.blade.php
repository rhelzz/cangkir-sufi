@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-8 pb-24 mt-6 md:mt-8">
    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 gap-4 md:gap-6 md:grid-cols-4 overflow-x-hidden">
        @php
            function rupiah($value) {
                return 'Rp ' . number_format($value, 0, ',', '.');
            }
            $cards = [
                [
                    'title' => "Today's Sales",
                    'icon' => 'fas fa-dollar-sign',
                    'color' => 'from-blue-500 via-blue-400 to-blue-300',
                    'value' => rupiah($todaySales),
                ],
                [
                    'title' => "Today's Expenses",
                    'icon' => 'fas fa-money-bill-wave',
                    'color' => 'from-rose-500 via-rose-400 to-rose-300',
                    'value' => rupiah($todayExpenses),
                ],
                [
                    'title' => "Today's Profit",
                    'icon' => 'fas fa-chart-line',
                    'color' => 'from-green-500 via-green-400 to-green-300',
                    'value' => rupiah($todayProfit),
                ],
                [
                    'title' => "Transactions",
                    'icon' => 'fas fa-shopping-cart',
                    'color' => 'from-purple-500 via-purple-400 to-purple-300',
                    'value' => $todayTransactions,
                ]
            ];
        @endphp

        @foreach($cards as $card)
        <div class="relative bg-gradient-to-br {{ $card['color'] }} rounded-2xl shadow-xl p-3 md:p-5 overflow-hidden flex flex-col items-start transition-transform duration-200 hover:scale-[1.035] hover:shadow-2xl min-h-[88px] md:min-h-[130px] select-none">
            <div class="absolute right-3 top-3 opacity-10 text-4xl md:text-6xl pointer-events-none select-none">
                <i class="{{ $card['icon'] }}"></i>
            </div>
            <div class="flex items-center z-10">
                <span class="flex items-center justify-center w-8 h-8 md:w-12 md:h-12 bg-white bg-opacity-25 rounded-full shadow text-white mr-2 md:mr-4 text-lg md:text-2xl">
                    <i class="{{ $card['icon'] }}"></i>
                </span>
                <div>
                    <h2 class="text-white text-xs md:text-sm font-semibold tracking-wide">{{ $card['title'] }}</h2>
                    <div class="text-base md:text-2xl font-black text-white drop-shadow mt-1 md:mt-2 break-all leading-tight">
                        {{ $card['value'] }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3 overflow-x-hidden">
        <!-- Sales Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-4 md:p-6 flex flex-col min-h-[240px] md:min-h-[320px]">
            <div class="flex items-center justify-between mb-2 md:mb-4">
                <h2 class="text-lg md:text-xl font-bold text-gray-800">Sales Last 7 Days</h2>
            </div>
            <div class="flex-1 min-h-[150px] sm:min-h-[230px] md:min-h-[320px]">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white rounded-2xl shadow-lg p-4 md:p-6 flex flex-col min-h-[240px] md:min-h-[320px]">
            <div class="flex items-center justify-between mb-2 md:mb-4">
                <h2 class="text-lg md:text-xl font-bold text-gray-800">Last 5 Transactions</h2>
            </div>
            <div class="overflow-x-auto -mx-2">
                <table class="min-w-full text-xs md:text-sm">
                    <thead class="bg-gray-50 sticky top-0 z-10">
                        <tr>
                            <th class="px-3 py-2 text-left font-semibold text-gray-500">Order #</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-500">Time</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-500">Amount</th>
                            <th class="px-3 py-2 text-left font-semibold text-gray-500">Cashier</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($latestTransactions as $transaction)
                        <tr class="hover:bg-blue-50 transition-colors duration-150">
                            <td class="px-3 py-2 whitespace-nowrap font-bold">
                                <a href="{{ route('cashier.view-order', $transaction->id) }}" class="text-blue-600 hover:underline">
                                    {{ $transaction->order_number }}
                                </a>
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-600">
                                {{ $transaction->created_at->format('H:i:s') }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap font-semibold text-gray-800">
                                {{ 'Rp ' . number_format($transaction->final_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-3 py-2 whitespace-nowrap text-gray-600">
                                {{ $transaction->user->name }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-3 py-8 text-center text-gray-400 italic">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fas fa-receipt text-2xl opacity-50"></i>
                                    <span>No transactions yet today</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Low Stock Products -->
    <div class="bg-white rounded-2xl shadow-lg p-4 md:p-6 overflow-x-auto">
        <div class="flex items-center justify-between mb-2 md:mb-4">
            <h2 class="text-lg md:text-xl font-bold text-gray-800">Low Stock Products</h2>
        </div>
        <div class="overflow-x-auto -mx-2">
            <table class="min-w-full text-xs md:text-sm">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-3 py-2 text-left font-semibold text-gray-500">Product</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-500">Category</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-500">Stock</th>
                        <th class="px-3 py-2 text-left font-semibold text-gray-500">Price</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($lowStockProducts as $product)
                    <tr class="hover:bg-red-50 transition-colors duration-150">
                        <td class="px-3 py-2 whitespace-nowrap flex items-center gap-2 md:gap-3">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-7 w-7 md:h-9 md:w-9 rounded-full object-cover border border-gray-200 shadow-inner hover:scale-110 transition-transform duration-150">
                            @endif
                            <a href="{{ route('products.show', $product->id) }}" class="text-blue-600 hover:underline font-medium">
                                {{ $product->name }}
                            </a>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-gray-600">
                            {{ $product->category->name }}
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs font-bold rounded-full bg-gradient-to-r from-rose-100 to-rose-200 text-rose-700">
                                {{ $product->stock }}
                            </span>
                        </td>
                        <td class="px-3 py-2 whitespace-nowrap text-gray-800">
                            {{ 'Rp ' . number_format($product->selling_price, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-3 py-8 text-center text-gray-400 italic">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-box-open text-2xl opacity-50"></i>
                                <span>No products with low stock</span>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('{{ route("dashboard.chart-data") }}')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('salesChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Sales',
                            data: data.salesData,
                            backgroundColor: 'rgba(59, 130, 246, 0.7)',
                            borderRadius: 7,
                            maxBarThickness: 32
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        layout: {
                            padding: { left: 0, right: 0, top: 10, bottom: 0 }
                        },
                        scales: {
                            x: {
                                grid: { display: false },
                                ticks: { font: { weight: 'bold' } }
                            },
                            y: {
                                beginAtZero: true,
                                grid: { color: "#f3f4f6" },
                                ticks: {
                                    callback: function(value) {
                                        // Format as Rupiah
                                        return 'Rp ' + value.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        return 'Sales: Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    }
                                }
                            }
                        }
                    }
                });
            });
    });
</script>
@endpush