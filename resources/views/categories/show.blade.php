@extends('layouts.app')

@section('title', 'Category Details')
@section('page-title', 'Category Details')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 p-5 border-b bg-gradient-to-r from-blue-50 to-white">
            <h2 class="text-xl font-bold flex items-center gap-2">
                <i class="fas fa-layer-group text-blue-400"></i>
                Category: {{ $category->name }}
            </h2>
            <div class="flex gap-2 mt-2 sm:mt-0">
                <a href="{{ route('categories.edit', $category->id) }}" class="inline-flex items-center bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm shadow transition-colors duration-200">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('categories.index') }}" class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm shadow transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
        
        <div class="p-4 md:p-6">
            <!-- Category Information -->
            <div class="mb-7">
                <h3 class="text-lg font-semibold mb-3 flex items-center gap-2 text-blue-900">
                    <i class="fas fa-info-circle text-blue-400"></i>
                    Category Information
                </h3>
                <div class="bg-gray-50 p-4 rounded-lg border flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex-1">
                        <div class="flex gap-3 items-center mb-2">
                            <span class="text-gray-500">Name:</span>
                            <span class="font-bold text-lg text-gray-800">{{ $category->name }}</span>
                        </div>
                        <div class="flex gap-3 items-center">
                            <span class="text-gray-500">Type:</span>
                            @if($category->type == 'hot')
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-red-100 text-red-700 font-semibold gap-1 text-xs">
                                    <i class="fas fa-mug-hot"></i> Hot
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-700 font-semibold gap-1 text-xs">
                                    <i class="fas fa-ice-cream"></i> Cold
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex-shrink-0 flex items-center gap-2 mt-3 md:mt-0">
                        <span class="inline-flex items-center px-2 py-1 rounded bg-gray-100 text-gray-700 text-xs gap-1">
                            <i class="fas fa-boxes-stacked"></i>
                            {{ $category->products->count() }} {{ Str::plural('Product', $category->products->count()) }}
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Products in this Category -->
            <div>
                <h3 class="text-lg font-semibold mb-3 flex items-center gap-2 text-blue-900">
                    <i class="fas fa-box text-blue-400"></i>
                    Products in this Category
                </h3>
                @if($category->products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($category->products as $product)
                    <div class="bg-white border rounded-xl shadow group hover:shadow-lg transition overflow-hidden flex flex-col">
                        <div class="h-32 bg-gray-100 flex items-center justify-center overflow-hidden relative">
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
                            @else
                                <i class="fas fa-box text-gray-300 text-4xl"></i>
                            @endif
                            <div class="absolute top-2 right-2">
                                @if($product->stock > 10)
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-green-100 text-green-700">Stock: {{ $product->stock }}</span>
                                @elseif($product->stock > 0)
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-yellow-100 text-yellow-700">Stock: {{ $product->stock }}</span>
                                @else
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-red-100 text-red-700">Out of stock</span>
                                @endif
                            </div>
                        </div>
                        <div class="p-4 flex-1 flex flex-col">
                            <h4 class="font-bold text-gray-900 text-base truncate mb-1" title="{{ $product->name }}">{{ $product->name }}</h4>
                            <p class="text-sm text-gray-600 mb-2">Price: <span class="font-semibold text-green-700">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span></p>
                            <a href="{{ route('products.show', $product->id) }}" class="mt-auto text-blue-600 hover:text-blue-800 text-xs font-semibold flex items-center gap-1">
                                <i class="fas fa-info-circle"></i> View details
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="bg-yellow-50 text-yellow-700 text-center rounded-lg py-8 mt-4">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    No products in this category yet.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection