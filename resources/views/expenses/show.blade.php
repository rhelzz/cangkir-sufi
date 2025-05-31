@extends('layouts.app')

@section('title', 'Expense Details')
@section('page-title', 'Expense Details')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b">
            <h2 class="text-xl font-bold">Expense Information</h2>
            <div class="space-x-2">
                @if(auth()->user()->isOwner())
                <a href="{{ route('expenses.edit', $expense->id) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md transition-colors duration-200">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                @endif
                <a href="{{ route('expenses.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
        
        <div class="p-6">
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Description</h3>
                    <p class="text-lg font-semibold">{{ $expense->description }}</p>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Amount</h3>
                    <p class="text-lg font-semibold">{{ number_format($expense->amount, 0) }}</p>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Expense Date</h3>
                    <p class="text-lg font-semibold">{{ $expense->expense_date->format('d M Y') }}</p>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Added By</h3>
                    <p class="text-lg font-semibold">{{ $expense->user->name }}</p>
                </div>
                
                <div class="mb-4">
                    <h3 class="text-sm font-medium text-gray-500">Created At</h3>
                    <p class="text-lg font-semibold">{{ $expense->created_at->format('d M Y H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection