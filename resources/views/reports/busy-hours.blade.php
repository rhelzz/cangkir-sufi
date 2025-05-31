@extends('layouts.app')

@section('title', 'Busy Hours Report')
@section('page-title', 'Busy Hours Report')

@section('content')
@php
    // Calculate the display period label for clarity
    $displayPeriod = '';
    if ($period === 'custom') {
        $displayPeriod = $fromDate->format('d M Y') . ' - ' . $toDate->format('d M Y');
    } elseif ($period === 'daily') {
        $displayPeriod = $fromDate->format('l, d M Y');
    } elseif ($period === 'weekly') {
        $weekStart = $fromDate->copy()->startOfWeek();
        $weekEnd = $fromDate->copy()->endOfWeek();
        $displayPeriod = 'Week of ' . $weekStart->format('d M Y') . ' - ' . $weekEnd->format('d M Y');
    } elseif ($period === 'monthly') {
        $displayPeriod = $fromDate->format('F Y');
    } elseif ($period === 'yearly') {
        $displayPeriod = $fromDate->format('Y');
    }
@endphp
<div class="bg-white rounded-2xl shadow-lg overflow-hidden">
    <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-white">
        <h2 class="text-2xl font-bold flex items-center gap-2 mb-4">
            <i class="fas fa-clock text-blue-400"></i>
            Busy Hours Report
        </h2>
        <form action="{{ route('reports.busy-hours') }}" method="GET" class="flex flex-wrap gap-4 items-end" id="filterForm">
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
                    <input type="date" name="from_date" id="from_date"
                        value="{{ $period === 'custom' ? ($fromDate ? $fromDate->format('Y-m-d') : '') : '' }}"
                        class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        {{ $period !== 'custom' ? 'disabled' : '' }}>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                    <input type="date" name="to_date" id="to_date"
                        value="{{ $period === 'custom' ? ($toDate ? $toDate->format('Y-m-d') : '') : '' }}"
                        class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        {{ $period !== 'custom' ? 'disabled' : '' }}>
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
        <!-- Hourly Traffic Listing -->
        <div class="mb-10">
            <h3 class="font-semibold text-lg mb-3 flex items-center gap-2 text-blue-900">
                <i class="fas fa-hourglass-half text-blue-400"></i>
                Top 5 Busy Hours
                <span class="text-xs font-normal text-gray-500 ml-2">
                    ({{ $displayPeriod }})
                </span>
            </h3>
            <div class="flex flex-col gap-2">
                @foreach($top5Hours as $i => $item)
                <div class="flex items-center justify-between p-4 rounded-lg border bg-gray-50 shadow-sm
                    {{ $i === 0 ? 'ring-2 ring-blue-300 bg-blue-50' : '' }}">
                    <div class="flex items-center gap-3">
                        <div class="flex flex-col items-center w-8">
                            @if($i === 0)
                                <span class="mb-1 text-blue-500 text-base">
                                    <i class="fas fa-crown"></i>
                                </span>
                            @endif
                            <span class="text-xl font-bold text-blue-700 text-center leading-none">{{ $i+1 }}</span>
                        </div>
                        <span class="font-semibold text-gray-800">{{ $item['label'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold min-h-[22px] !leading-none">
                            <i class="fas fa-receipt"></i> {{ number_format($item['orders']) }} Orders
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-semibold min-h-[22px] !leading-none">
                            <i class="fas fa-money-bill-wave"></i> Rp {{ number_format($item['sales']) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        @if($period !== 'daily')
        <!-- Daily Traffic Listing -->
        <div>
            <h3 class="font-semibold text-lg mb-3 flex items-center gap-2 text-blue-900">
                <i class="fas fa-calendar-day text-blue-400"></i>
                Top 5 Busiest Days
                <span class="text-xs font-normal text-gray-500 ml-2">
                    ({{ $displayPeriod }})
                </span>
            </h3>
            <div class="flex flex-col gap-2">
                @foreach($top5Days as $i => $item)
                <div class="flex items-center justify-between p-4 rounded-lg border bg-gray-50 shadow-sm
                    {{ $i === 0 ? 'ring-2 ring-amber-300 bg-yellow-50' : '' }}">
                    <div class="flex items-center gap-3">
                        <div class="flex flex-col items-center w-8">
                            @if($i === 0)
                                <span class="mb-1 text-amber-500 text-base">
                                    <i class="fas fa-crown"></i>
                                </span>
                            @endif
                            <span class="text-xl font-bold text-amber-700 text-center leading-none">{{ $i+1 }}</span>
                        </div>
                        <span class="font-semibold text-gray-800">{{ $item['label'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800 text-xs font-semibold min-h-[22px] !leading-none">
                            <i class="fas fa-receipt"></i> {{ number_format($item['orders']) }} Orders
                        </span>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-green-100 text-green-700 text-xs font-semibold min-h-[22px] !leading-none">
                            <i class="fas fa-money-bill-wave"></i> Rp {{ number_format($item['sales']) }}
                        </span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const periodSelect = document.getElementById('period');
    const dateRangeContainer = document.getElementById('date-range-container');
    const fromDateInput = document.getElementById('from_date');
    const toDateInput = document.getElementById('to_date');
    const filterForm = document.getElementById('filterForm');

    function toggleDateRange() {
        if (periodSelect.value === 'custom') {
            dateRangeContainer.classList.remove('hidden');
            fromDateInput.disabled = false;
            toDateInput.disabled = false;
        } else {
            dateRangeContainer.classList.add('hidden');
            fromDateInput.value = '';
            toDateInput.value = '';
            fromDateInput.disabled = true;
            toDateInput.disabled = true;
        }
    }

    // On period change
    periodSelect.addEventListener('change', toggleDateRange);

    // On form submit, if period != custom, remove the date fields from the GET request
    filterForm.addEventListener('submit', function(e) {
        if (periodSelect.value !== 'custom') {
            fromDateInput.disabled = true;
            toDateInput.disabled = true;
        }
    });
});
</script>
@endpush