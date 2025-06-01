@extends('layouts.app')

@section('title', 'Expenses')
@section('page-title', 'Daily Expenses')

@section('content')
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3 p-4 md:p-6 border-b bg-gradient-to-r from-red-50 to-white">
        <div class="flex items-center gap-3">
            <i class="fas fa-file-invoice-dollar text-red-500 text-xl"></i>
            <h2 class="text-lg md:text-xl font-bold text-gray-800">Expenses for {{ $date->format('d M Y') }}</h2>
        </div>
        <div class="flex flex-col md:flex-row gap-2">
            <form action="{{ route('expenses.index') }}" method="GET" class="flex gap-2">
                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" 
                    class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 shadow-sm">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors duration-200 shadow-sm flex items-center">
                    <i class="fas fa-calendar-alt mr-2"></i> Change Date
                </button>
            </form>
            <a href="{{ route('expenses.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-colors duration-200 shadow-sm flex items-center justify-center">
                <i class="fas fa-plus mr-2"></i> Add Expense
            </a>
        </div>
    </div>
    
    <div class="p-4 md:p-6">
        <!-- Total Amount Card -->
        <div class="mb-6 bg-gradient-to-r from-red-50 to-red-100 border border-red-200 rounded-lg p-4 shadow-sm transition-all duration-300 hover:shadow-md">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-red-700"><i class="fas fa-chart-bar mr-2"></i>Total Expenses Today</h3>
                    <p class="text-sm text-gray-600">{{ $date->format('d M Y') }}</p>
                </div>
                <div class="text-2xl font-bold text-red-700">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
            </div>
        </div>
        
        <!-- Expenses List (will be populated by JS) -->
        <div id="expensesList" class="flex flex-col gap-3 md:gap-4"></div>
        
        <!-- Pagination controls (will be populated by JS) -->
        <div class="mt-6 flex justify-between items-center" id="expensesPaginationContainer">
            <div id="expensesPaginationInfo" class="text-xs text-gray-500"></div>
            <div id="expensesPagination" class="flex gap-1"></div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const expensesData = [
    @foreach($expenses as $expense)
    {
        id: {{ $expense->id }},
        description: @json($expense->description),
        items_count: {{ $expense->items->count() }},
        total_amount: {{ $expense->total_amount }},
        total_amount_formatted: "{{ number_format($expense->total_amount, 0, ',', '.') }}",
        user_name: @json($expense->user->name),
        user_initials: @json(substr($expense->user->name, 0, 2)),
        created_time: "{{ $expense->created_at->format('H:i:s') }}",
        show_url: "{{ route('expenses.show', $expense->id) }}",
        edit_url: "{{ route('expenses.edit', $expense->id) }}",
        delete_url: "{{ route('expenses.destroy', $expense->id) }}",
        can_edit: {{ auth()->user()->isOwner() ? 'true' : 'false' }}
    },
    @endforeach
];
const PAGE_SIZE = 5;
let currentPage = 1;

function renderExpensesList() {
    const container = document.getElementById('expensesList');
    container.innerHTML = '';
    const start = (currentPage-1)*PAGE_SIZE, end = start+PAGE_SIZE;
    const pageExpenses = expensesData.slice(start, end);

    if(pageExpenses.length === 0) {
        container.innerHTML = `
            <div class="col-span-1 bg-white rounded-lg border border-gray-200 shadow-sm p-8">
                <div class="flex flex-col items-center justify-center">
                    <i class="fas fa-receipt text-gray-300 text-5xl mb-3"></i>
                    <p class="text-gray-700 font-medium text-lg">No expenses found for this date</p>
                    <p class="text-gray-500 text-md mt-1 mb-4">Try adding a new expense or changing the date</p>
                    <a href="{{ route('expenses.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md transition-colors duration-200 shadow-sm flex items-center justify-center">
                        <i class="fas fa-plus mr-2"></i> Add New Expense
                    </a>
                </div>
            </div>`;
        renderExpensesPagination(1, 1, 0, 0);
        return;
    }
    
    for(const expense of pageExpenses) {
        container.innerHTML += `
        <div class="group flex flex-col md:flex-row gap-4 p-4 md:p-5 rounded-lg border shadow-sm hover:shadow-md transition bg-gray-50 md:bg-white">
            <div class="flex-grow">
                <div class="flex items-center gap-3 mb-3">
                    <div class="h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                        <span class="text-xs font-medium text-blue-600">${expense.user_initials}</span>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-gray-700">#${expense.id}</div>
                        <div class="text-xs text-gray-500">
                            <i class="far fa-clock mr-1"></i> ${expense.created_time}
                        </div>
                    </div>
                    <span class="ml-auto px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        ${expense.items_count} items
                    </span>
                </div>

                <h3 class="text-md font-medium text-gray-900 mb-2 line-clamp-1" title="${expense.description}">
                    ${expense.description}
                </h3>

                <div class="flex flex-wrap gap-y-2 gap-x-4 mb-3">
                    <div class="flex items-center gap-1">
                        <span class="text-sm text-gray-500">Added by:</span>
                        <span class="text-sm font-medium text-gray-800">${expense.user_name}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <span class="text-sm text-gray-500">Total:</span>
                        <span class="text-sm font-bold text-red-600">Rp ${expense.total_amount_formatted}</span>
                    </div>
                </div>
            </div>

            <div class="flex md:flex-col gap-2 md:border-l md:pl-4">
                <a href="${expense.show_url}" class="text-blue-600 hover:bg-blue-100 hover:text-blue-800 p-2 rounded" title="View Details">
                    <i class="fas fa-eye"></i>
                </a>
                ${expense.can_edit ? `
                <a href="${expense.edit_url}" class="text-yellow-600 hover:bg-yellow-100 hover:text-yellow-800 p-2 rounded" title="Edit Expense">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="${expense.delete_url}" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:bg-red-100 hover:text-red-800 p-2 rounded" title="Delete Expense"
                        onclick="event.preventDefault(); if(confirm('Are you sure you want to delete this expense?')) this.closest('form').submit();">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
                ` : ''}
            </div>
        </div>`;
    }
    renderExpensesPagination(Math.ceil(expensesData.length/PAGE_SIZE), currentPage, start+1, Math.min(end, expensesData.length));
}

function renderExpensesPagination(totalPages, activePage, startItem, endItem) {
    let info = '';
    if(expensesData.length > 0) {
        info = `Showing <b>${startItem}</b> - <b>${endItem}</b> of <b>${expensesData.length}</b>`;
    }
    document.getElementById('expensesPaginationInfo').innerHTML = info;
    
    // Pagination
    let html = '';
    if(totalPages > 1) {
        html += `<button ${activePage===1?'disabled':''} onclick="window.goToExpensePage(${activePage-1})" class="rounded px-2 py-1 ${activePage===1?'bg-gray-200 text-gray-400':'bg-white text-gray-600 border'}">&lt;</button>`;
        let min = Math.max(1, activePage-2), max = Math.min(totalPages, min+4);
        min = Math.max(1, max-4);
        for(let i=min; i<=max; i++) {
            html += `<button class="rounded px-2 py-1 ${i==activePage ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700'}" onclick="window.goToExpensePage(${i})">${i}</button>`;
        }
        html += `<button ${activePage===totalPages?'disabled':''} onclick="window.goToExpensePage(${activePage+1})" class="rounded px-2 py-1 ${activePage===totalPages?'bg-gray-200 text-gray-400':'bg-white text-gray-600 border'}">&gt;</button>`;
    }
    document.getElementById('expensesPagination').innerHTML = html;
}

window.goToExpensePage = function(page) {
    if(page < 1) return;
    currentPage = page;
    renderExpensesList();
}

document.addEventListener('DOMContentLoaded', function() {
    renderExpensesList();
});
</script>
@endpush