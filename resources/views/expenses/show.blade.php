@extends('layouts.app')

@section('title', 'Expense Details')
@section('page-title', 'Expense Details')

@section('content')
<div class="max-w-lg mx-auto px-2 py-3">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="flex flex-col sm:flex-row justify-between items-center px-4 py-3 border-b gap-2">
            <h2 class="text-base font-bold">Expense Information</h2>
            <div class="flex gap-2 w-full sm:w-auto">
                @if(auth()->user()->isOwner())
                <a href="{{ route('expenses.edit', $expense->id) }}" class="flex-1 sm:flex-initial bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded text-xs font-semibold flex items-center justify-center">
                    <i class="fas fa-edit mr-1"></i> Edit
                </a>
                @endif
                <a href="{{ route('expenses.index') }}" class="flex-1 sm:flex-initial bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1.5 rounded text-xs font-semibold flex items-center justify-center">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
            </div>
        </div>
        
        <div class="px-4 py-4">
            <div class="bg-gray-50 p-3 rounded-lg mb-5">
                <div class="mb-3">
                    <div class="text-xs text-gray-500 font-semibold">Description</div>
                    <div class="text-base font-bold text-gray-800 truncate">{{ $expense->description }}</div>
                </div>
                <div class="mb-3 flex justify-between items-center">
                    <div>
                        <div class="text-xs text-gray-500 font-semibold">Expense Date</div>
                        <div class="text-sm text-gray-800">{{ $expense->expense_date->format('d M Y') }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 font-semibold text-right">Total</div>
                        <div class="text-base font-bold text-red-600 text-right">Rp {{ number_format($expense->total_amount, 0) }}</div>
                    </div>
                </div>
                <div class="mb-3 flex justify-between items-center">
                    <div>
                        <div class="text-xs text-gray-500 font-semibold">Added By</div>
                        <div class="text-sm text-gray-800">{{ $expense->user->name }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 font-semibold text-right">Created At</div>
                        <div class="text-xs text-gray-600 text-right">{{ $expense->created_at->format('d M Y H:i') }}</div>
                    </div>
                </div>
            </div>
            
            <div class="mb-2 flex items-center justify-between">
                <h3 class="text-base font-bold">Items ({{ $expense->items->count() }})</h3>
            </div>
            <div class="flex flex-col gap-2">
                @forelse($expense->items as $item)
                <div class="flex justify-between items-center bg-white border rounded-lg p-2 shadow-sm">
                    <div>
                        <div class="text-sm font-semibold text-gray-800 truncate">{{ $item->item_name }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-xs text-gray-400">Price</div>
                        <div class="text-sm font-bold text-red-600">Rp {{ number_format($item->price, 0) }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center text-gray-400 italic py-5">No items found</div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection