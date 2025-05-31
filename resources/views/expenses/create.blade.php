@extends('layouts.app')

@section('title', 'Add Expense')
@section('page-title', 'Add New Expense')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b">
            <h2 class="text-xl font-bold">Add New Expense</h2>
            <a href="{{ route('expenses.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
        
        <div class="p-6">
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <input type="text" name="description" id="description" value="{{ old('description') }}" 
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('description') border-red-500 @enderror" required>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="amount" class="block text-gray-700 text-sm font-bold mb-2">Amount</label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" 
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('amount') border-red-500 @enderror" 
                        min="0" step="0.01" required>
                    @error('amount')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label for="expense_date" class="block text-gray-700 text-sm font-bold mb-2">Expense Date</label>
                    <input type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', now()->format('Y-m-d')) }}" 
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('expense_date') border-red-500 @enderror" required>
                    @error('expense_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i> Save Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection