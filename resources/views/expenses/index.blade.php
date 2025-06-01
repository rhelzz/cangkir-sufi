@extends('layouts.app')

@section('title', 'Expenses')
@section('page-title', 'Daily Expenses')

@section('content')
<div class="px-2 py-3">
    <!-- Filter & Add -->
    <form action="{{ route('expenses.index') }}" method="GET" class="flex flex-col gap-2 mb-3">
        <div class="flex flex-col gap-2">
            <label class="text-xs font-semibold text-gray-700">Date range</label>
            <div class="flex gap-2">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="flex-1 px-2 py-2 rounded border focus:ring-2 focus:ring-blue-500 text-sm" />
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="flex-1 px-2 py-2 rounded border focus:ring-2 focus:ring-blue-500 text-sm" />
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded transition-colors duration-200 text-sm flex items-center">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
            </div>
        </div>
        <a href="{{ route('expenses.create') }}" class="w-full bg-green-600 hover:bg-green-700 text-white mt-2 py-2 rounded text-base font-bold text-center flex items-center justify-center transition">
            <i class="fas fa-plus mr-2"></i> Add
        </a>
    </form>

    <!-- Total Card -->
    <div class="mb-3 bg-red-50 border border-red-300 rounded-lg p-3 shadow flex flex-col">
        <div class="flex justify-between items-center">
            <div>
                <div class="text-xs text-red-600 font-bold mb-1">Total Expenses</div>
                <div class="text-xs text-gray-500">
                    @if(isset($isRange) && $isRange)
                        {{ $dateFrom->format('d M Y') }} — {{ $dateTo->format('d M Y') }}
                    @else
                        {{ $date->format('d M Y') }}
                    @endif
                </div>
            </div>
            <div class="text-lg font-bold text-red-600 ml-2">
                Rp {{ number_format($totalAmount, 0) }}
            </div>
        </div>
    </div>

    <!-- Expenses List -->
    <div class="flex flex-col gap-2">
        @forelse($expenses as $expense)
        <div class="rounded-xl border border-gray-100 bg-white p-3 shadow flex flex-col gap-1">
            <div class="flex justify-between items-center">
                <div class="flex items-center gap-2 min-w-0">
                    <span class="text-blue-500 font-bold text-xs">#{{ $expense->id }}</span>
                    <span class="expense-desc truncate text-sm text-gray-800 font-medium max-w-[8rem] sm:max-w-xs">{{ $expense->description }}</span>
                </div>
                <span class="expense-amount bg-red-100 text-red-700 font-bold text-xs rounded px-2 py-1">Rp {{ number_format($expense->total_amount, 0) }}</span>
            </div>
            <div class="flex justify-between items-center text-xs text-gray-400">
                <div class="flex gap-2 items-center">
                    <span><i class="fas fa-user"></i> {{ $expense->user->name }}</span>
                    <span><i class="fas fa-clock"></i> {{ $expense->created_at->format('H:i') }}</span>
                    <span>{{ $expense->items->count() }} items</span>
                </div>
                <div class="flex gap-1 items-center">
                    <a href="{{ route('expenses.show', $expense->id) }}" title="View" class="text-gray-500 hover:text-blue-600 px-1 py-1 rounded"><i class="fas fa-eye"></i></a>
                    @if(auth()->user()->isOwner())
                    <a href="{{ route('expenses.edit', $expense->id) }}" title="Edit" class="text-gray-500 hover:text-yellow-500 px-1 py-1 rounded"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?');" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit" title="Delete" class="text-gray-500 hover:text-red-500 px-1 py-1 rounded"><i class="fas fa-trash"></i></button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="text-center text-gray-400 italic py-8">No expenses found for this date</div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex justify-center">
        {{ $expenses->links() }}
    </div>

    <style>
    @media (max-width: 600px) {
        .expense-desc { max-width: 110px !important; }
    }
    </style>
</div>
@endsection