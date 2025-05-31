@extends('layouts.app')

@section('title', 'Sales Report')
@section('page-title', 'Sales Report')

@section('content')
<div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    <!-- Header -->
    <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-white">
        <h2 class="text-2xl font-bold flex items-center gap-2 mb-4">
            <i class="fas fa-chart-line text-blue-400"></i>
            Sales Report
        </h2>
        <!-- Date Filter -->
        <form action="{{ route('reports.sales') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Period</label>
                <select name="period" id="period" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="daily" {{ $period === 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ $period === 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ $period === 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="yearly" {{ $period === 'yearly' ? 'selected' : '' }}>Yearly</option>
                    <option value="custom" {{ $period === 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>
            
            <div id="date-range-container" class="flex gap-2 {{ $period !== 'custom' ? 'hidden' : '' }}">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                    <input type="date" name="from_date" value="{{ $fromDate ? $fromDate->format('Y-m-d') : '' }}" 
                        class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                    <input type="date" name="to_date" value="{{ $toDate ? $toDate->format('Y-m-d') : '' }}" 
                        class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            
            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md font-semibold shadow transition-colors duration-200">
                    <i class="fas fa-filter mr-2"></i> Apply Filter
                </button>
            </div>
        </form>
    </div>
    
    <div class="p-4 md:p-6">
        <!-- Summary -->
        <div class="mb-8">
            <h3 class="font-semibold text-lg mb-3 flex items-center gap-2 text-blue-900">
                <i class="fas fa-clipboard-list text-blue-400"></i>
                Summary
                <span class="text-xs font-normal text-gray-500 ml-2">
                    ({{ $fromDate->format('d M Y') }} - {{ $toDate->format('d M Y') }})
                </span>
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100 shadow-sm flex flex-col gap-1">
                    <div class="text-xs font-medium text-blue-700 flex items-center gap-2">
                        <i class="fas fa-receipt"></i> Total Orders
                    </div>
                    <div class="text-2xl font-bold text-blue-900">{{ number_format($summary->total_orders) }}</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-100 shadow-sm flex flex-col gap-1">
                    <div class="text-xs font-medium text-green-700 flex items-center gap-2">
                        <i class="fas fa-dollar-sign"></i> Gross Sales
                    </div>
                    <div class="text-2xl font-bold text-green-900">{{ number_format($summary->total_sales) }}</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-100 shadow-sm flex flex-col gap-1">
                    <div class="text-xs font-medium text-purple-700 flex items-center gap-2">
                        <i class="fas fa-wallet"></i> Net Sales
                    </div>
                    <div class="text-2xl font-bold text-purple-900">{{ number_format($summary->final_sales) }}</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100 shadow-sm flex flex-col gap-1">
                    <div class="text-xs font-medium text-yellow-700 flex items-center gap-2">
                        <i class="fas fa-coins"></i> Average Sale
                    </div>
                    <div class="text-2xl font-bold text-yellow-900">{{ number_format($summary->avg_sale) }}</div>
                </div>
            </div>
        </div>
        
        <!-- Top Selling Products -->
        <div class="mb-8">
            <h3 class="font-semibold text-lg mb-3 flex items-center gap-2 text-blue-900">
                <i class="fas fa-star text-yellow-400"></i>
                Top Selling Products
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 rounded-xl overflow-hidden">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Sold</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($topProducts as $product)
                        <tr class="hover:bg-blue-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($product->product->image)
                                        <img class="h-10 w-10 rounded object-cover mr-3 shadow-sm border" src="{{ asset('storage/' . $product->product->image) }}" alt="{{ $product->product->name }}">
                                    @else
                                        <span class="flex items-center justify-center h-10 w-10 rounded bg-gray-100 mr-3 border">
                                            <i class="fas fa-box text-gray-300 text-xl"></i>
                                        </span>
                                    @endif
                                    <div class="text-sm font-medium text-gray-900">{{ $product->product->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $product->product->category->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ number_format($product->total_qty) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-green-800">
                                {{ number_format($product->total_amount) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500 italic">No sales data available for this period</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Sales Chart -->
        <div class="mb-4">
            <h3 class="font-semibold text-lg mb-3 flex items-center gap-2 text-blue-900">
                <i class="fas fa-chart-bar text-blue-400"></i>
                Sales Chart
            </h3>
            <div class="bg-white p-4 rounded-xl border shadow h-80">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle date range inputs based on period selection
        const periodSelect = document.getElementById('period');
        const dateRangeContainer = document.getElementById('date-range-container');
        
        periodSelect.addEventListener('change', function() {
            if (this.value === 'custom') {
                dateRangeContainer.classList.remove('hidden');
            } else {
                dateRangeContainer.classList.add('hidden');
            }
        });

        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const labels = @json($topProducts->pluck('product.name')->take(5));
        const quantities = @json($topProducts->pluck('total_qty')->take(5));
        const amounts = @json($topProducts->pluck('total_amount')->take(5));
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Quantity Sold',
                        data: quantities,
                        backgroundColor: 'rgba(59, 130, 246, 0.65)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 1,
                        borderRadius: 8,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Revenue',
                        data: amounts,
                        backgroundColor: 'rgba(16, 185, 129, 0.50)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 1,
                        borderRadius: 8,
                        yAxisID: 'y1'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            font: { size: 13 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                label += context.parsed.y.toLocaleString();
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Quantity'
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            stepSize: 1
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Revenue'
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endpush