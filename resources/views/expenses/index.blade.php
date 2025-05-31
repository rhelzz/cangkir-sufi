@extends('layouts.app')

@section('title', 'Expenses')
@section('page-title', 'Daily Expenses')

@section('content')
<div class="bg-white rounded-lg shadow-md overflow-hidden">
    <div class="flex flex-col md:flex-row justify-between items-center p-6 border-b">
        <h2 class="text-xl font-bold mb-2 md:mb-0">Expenses for {{ $date->format('d M Y') }}</h2>
        <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-2">
            <form action="{{ route('expenses.index') }}" method="GET" class="flex space-x-2">
                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" 
                    class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors duration-200">
                    <i class="fas fa-calendar-alt mr-2"></i> Change Date
                </button>
            </form>
            <a href="{{ route('expenses.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors duration-200 text-center">
                <i class="fas fa-plus mr-2"></i> Add Expense
            </a>
        </div>
    </div>
    
    <div class="p-6">
        <!-- Total Amount Card -->
        <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-red-700">Total Expenses Today</h3>
                    <p class="text-sm text-gray-600">{{ $date->format('d M Y') }}</p>
                </div>
                <div class="text-2xl font-bold text-red-700">{{ number_format($totalAmount, 0) }}</div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Added By</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($expenses as $expense)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $expense->description }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ number_format($expense->amount, 0) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $expense->user->name }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500">{{ $expense->created_at->format('H:i:s') }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('expenses.show', $expense->id) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(auth()->user()->isOwner())
                                <a href="{{ route('expenses.edit', $expense->id) }}" class="text-yellow-600 hover:text-yellow-900">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this expense?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500 italic">No expenses found for this date</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $expenses->links() }}
        </div>
    </div>
</div>
@endsection