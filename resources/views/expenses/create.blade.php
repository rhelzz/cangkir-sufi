@extends('layouts.app')

@section('title', 'Add Expense')
@section('page-title', 'Add New Expense')

@section('content')
<div class="max-w-lg mx-auto px-2 py-3">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="flex justify-between items-center px-4 py-3 border-b">
            <h2 class="text-base font-bold">Add Expense</h2>
            <a href="{{ route('expenses.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-1 text-xs rounded transition flex items-center">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>
        
        <div class="px-4 py-4">
            <form action="{{ route('expenses.store') }}" method="POST" autocomplete="off">
                @csrf
                
                <div class="mb-3">
                    <label for="description" class="block text-xs font-semibold text-gray-700 mb-1">Description</label>
                    <input type="text" name="description" id="description" value="{{ old('description') }}" 
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm @error('description') border-red-500 @enderror" required>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="expense_date" class="block text-xs font-semibold text-gray-700 mb-1">Expense Date</label>
                    <input type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', now()->format('Y-m-d')) }}" 
                        class="w-full px-3 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm @error('expense_date') border-red-500 @enderror" required>
                    @error('expense_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-xs font-semibold text-gray-700">Items</label>
                        <button type="button" id="add-item" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 text-xs rounded transition flex items-center">
                            <i class="fas fa-plus mr-1"></i> Add Item
                        </button>
                    </div>
                    @error('items')
                        <p class="text-red-500 text-xs mt-1 mb-2">{{ $message }}</p>
                    @enderror
                    
                    <div id="items-container" class="flex flex-col gap-2"></div>
                </div>
                
                <div class="flex items-center justify-between mt-6 border-t pt-3">
                    <div class="text-sm font-semibold">
                        Total: <span id="total-amount" class="text-blue-600">Rp 0</span>
                    </div>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded text-sm font-bold flex items-center">
                        <i class="fas fa-save mr-2"></i> Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemsContainer = document.getElementById('items-container');
        const addItemButton = document.getElementById('add-item');
        let itemCount = 0;
        
        // Add first item by default
        addItem();
        
        addItemButton.addEventListener('click', function() {
            addItem();
        });
        
        function addItem() {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'flex gap-2 items-center item-row';
            itemDiv.dataset.index = itemCount;
            
            itemDiv.innerHTML = `
                <input type="text" name="items[${itemCount}][item_name]" placeholder="Item name" 
                    class="flex-1 px-2 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm"
                    required>
                <input type="number" name="items[${itemCount}][price]" placeholder="Price" min="0" step="0.01"
                    class="w-[5.5rem] px-2 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-400 text-sm item-price"
                    required>
                <button type="button" class="delete-item bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs flex items-center" title="Remove">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            
            itemsContainer.appendChild(itemDiv);
            
            // Delete button
            const deleteButton = itemDiv.querySelector('.delete-item');
            deleteButton.addEventListener('click', function() {
                if (document.querySelectorAll('.item-row').length > 1) {
                    itemDiv.remove();
                    updateTotal();
                }
            });
            
            // Update total on price input
            const priceInput = itemDiv.querySelector('.item-price');
            priceInput.addEventListener('input', updateTotal);
            
            itemCount++;
        }
        
        function updateTotal() {
            const priceInputs = document.querySelectorAll('.item-price');
            let total = 0;
            
            priceInputs.forEach(input => {
                const price = parseFloat(input.value) || 0;
                total += price;
            });
            
            // Format to "Rp 1.000" etc.
            document.getElementById('total-amount').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
    });
</script>
@endpush
@endsection