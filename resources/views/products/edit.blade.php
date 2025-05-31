@extends('layouts.app')

@section('title', 'Edit Product')
@section('page-title', 'Edit Product')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 p-5 border-b bg-gradient-to-r from-blue-50 to-white">
            <h2 class="text-xl font-bold flex items-center gap-2">
                <i class="fas fa-box text-blue-400"></i>
                Edit Product
            </h2>
            <a href="{{ route('products.index') }}" class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm shadow transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
        
        <div class="p-4 md:p-6">
            <form action="{{ route('products.update', $product->id) }}" method="POST" enctype="multipart/form-data" autocomplete="off">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Product Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" 
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror" required autofocus>
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="category_id" class="block text-gray-700 text-sm font-bold mb-2">Category <span class="text-red-500">*</span></label>
                        <select name="category_id" id="category_id" 
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('category_id') border-red-500 @enderror" 
                            required>
                            <option value="">Select a category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ (old('category_id', $product->category_id) == $category->id) ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ ucfirst($category->type) }})
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-4">
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                    <textarea name="description" id="description" rows="2" 
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <label for="selling_price" class="block text-gray-700 text-sm font-bold mb-2">Selling Price <span class="text-red-500">*</span></label>
                        <input type="number" name="selling_price" id="selling_price" value="{{ old('selling_price', $product->selling_price) }}" 
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('selling_price') border-red-500 @enderror" 
                            min="0" step="0.01" required>
                        @error('selling_price')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="stock" class="block text-gray-700 text-sm font-bold mb-2">Stock <span class="text-red-500">*</span></label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" 
                            class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('stock') border-red-500 @enderror" 
                            min="0" required>
                        @error('stock')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="col-span-2 md:col-span-1">
                        <label for="image" class="block text-gray-700 text-sm font-bold mb-2">Product Image</label>
                        <div class="flex items-center gap-4">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="h-16 w-16 object-cover rounded border">
                            @endif
                            <input type="file" name="image" id="image" 
                                class="flex-1 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('image') border-red-500 @enderror"
                                accept="image/*">
                        </div>
                        @error('image')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="mt-5">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Ingredients <span class="text-red-500">*</span></label>
                    <div id="ingredients-container" class="space-y-2">
                        @if(count($product->ingredients))
                            @foreach($product->ingredients as $index => $ingredient)
                                <div class="ingredient-item flex gap-2 items-center mb-1">
                                    <input type="text" name="ingredients[{{ $index }}][name]" value="{{ $ingredient->name }}" placeholder="Ingredient name"
                                        class="flex-1 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                    <input type="number" name="ingredients[{{ $index }}][price]" value="{{ $ingredient->price }}" placeholder="Price"
                                        class="w-28 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        min="0" step="0.01" required>
                                    <button type="button" class="remove-ingredient {{ count($product->ingredients) > 1 ? '' : 'hidden' }} text-red-500 hover:text-red-700" tabindex="-1" aria-label="Remove">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @else
                            <div class="ingredient-item flex gap-2 items-center mb-1">
                                <input type="text" name="ingredients[0][name]" placeholder="Ingredient name"
                                    class="flex-1 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                                <input type="number" name="ingredients[0][price]" placeholder="Price"
                                    class="w-28 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    min="0" step="0.01" required>
                                <button type="button" class="remove-ingredient hidden text-red-500 hover:text-red-700" tabindex="-1" aria-label="Remove">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                    <div class="mt-2">
                        <button type="button" id="add-ingredient" 
                            class="bg-green-500 hover:bg-green-600 text-white text-sm px-3 py-1 rounded-md transition-colors duration-200">
                            <i class="fas fa-plus mr-1"></i> Add Ingredient
                        </button>
                    </div>
                    @error('ingredients')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end mt-8">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-semibold shadow transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i> Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ingredientsContainer = document.getElementById('ingredients-container');
    const addIngredientBtn = document.getElementById('add-ingredient');
    let ingredientCount = {{ max(count($product->ingredients), 1) }};

    // Auto focus for efficiency
    document.getElementById('name').focus();

    addIngredientBtn.addEventListener('click', function() {
        const ingredientItem = document.createElement('div');
        ingredientItem.className = 'ingredient-item flex gap-2 items-center mb-1';
        ingredientItem.innerHTML = `
            <input type="text" name="ingredients[${ingredientCount}][name]" placeholder="Ingredient name"
                class="flex-1 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            <input type="number" name="ingredients[${ingredientCount}][price]" placeholder="Price"
                class="w-28 px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                min="0" step="0.01" required>
            <button type="button" class="remove-ingredient text-red-500 hover:text-red-700" tabindex="-1" aria-label="Remove">
                <i class="fas fa-times"></i>
            </button>
        `;
        ingredientsContainer.appendChild(ingredientItem);
        ingredientItem.querySelector('input').focus();
        ingredientCount++;
        updateRemoveButtons();
    });

    // Remove ingredient
    ingredientsContainer.addEventListener('click', function(e) {
        if(e.target.closest('.remove-ingredient')) {
            const item = e.target.closest('.ingredient-item');
            if(item) {
                item.remove();
                updateRemoveButtons();
            }
        }
    });

    function updateRemoveButtons() {
        const items = ingredientsContainer.querySelectorAll('.ingredient-item');
        items.forEach((item, idx) => {
            const removeBtn = item.querySelector('.remove-ingredient');
            if(items.length > 1) {
                removeBtn.classList.remove('hidden');
            } else {
                removeBtn.classList.add('hidden');
            }
        });
    }

    // Allow pressing Enter to quickly move to the next input in ingredient rows
    ingredientsContainer.addEventListener('keydown', function(e) {
        if(e.key === 'Enter') {
            e.preventDefault();
            const inputs = Array.from(ingredientsContainer.querySelectorAll('input'));
            const idx = inputs.indexOf(document.activeElement);
            if(idx >= 0 && idx < inputs.length - 1) {
                inputs[idx+1].focus();
            } else {
                addIngredientBtn.click();
            }
        }
    });

    // Initial state for remove buttons
    updateRemoveButtons();
});
</script>
@endpush