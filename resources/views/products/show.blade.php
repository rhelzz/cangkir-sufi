@extends('layouts.app')

@section('title', 'Product Details')
@section('page-title', 'Product Details')

@section('content')
<div class="max-w-3xl md:max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-5 border-b bg-gradient-to-r from-blue-50 to-white">
            <h2 class="text-lg md:text-2xl font-bold flex items-center gap-2">
                <i class="fas fa-box text-blue-400"></i>
                Product Information
            </h2>
            <div class="flex gap-2 mt-2 sm:mt-0">
                <a href="{{ route('products.edit', $product->id) }}" class="inline-flex items-center bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm shadow transition-colors duration-200">
                    <i class="fas fa-edit mr-2"></i> Edit
                </a>
                <a href="{{ route('products.index') }}" class="inline-flex items-center bg-gray-400 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm shadow transition-colors duration-200">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
        </div>
        
        <div class="p-5 md:p-7">
            <div class="flex flex-col md:flex-row md:space-x-8">
                <!-- Left: Image & Price Info -->
                <div class="md:w-1/3 mb-7 md:mb-0 flex flex-col gap-5">
                    <div>
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full rounded-xl shadow border object-cover max-h-72 bg-white">
                        @else
                            <div class="w-full h-56 bg-gray-100 rounded-xl flex items-center justify-center border">
                                <i class="fas fa-image text-gray-300 text-5xl"></i>
                            </div>
                        @endif
                    </div>
                    <div class="p-4 bg-gray-50 rounded-lg border">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-500 text-sm">Selling Price</span>
                            <span class="font-bold text-green-700 text-base">Rp {{ number_format($product->selling_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-500 text-sm">Cost Price</span>
                            <span class="font-bold text-gray-700 text-base">Rp {{ number_format($product->cost_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-gray-500 text-sm">Profit</span>
                            <span class="font-bold text-green-600 text-base">Rp {{ number_format($product->calculateProfit(), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-500 text-sm">Current Stock</span>
                            @if($product->stock > 10)
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800"> {{ $product->stock }} </span>
                            @elseif($product->stock > 0)
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800"> {{ $product->stock }} </span>
                            @else
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800"> Out of stock </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Right: Details -->
                <div class="md:w-2/3 flex flex-col gap-6">
                    <div>
                        <h1 class="text-2xl font-bold mb-2 text-primary-800">{{ $product->name }}</h1>
                        <div class="mb-4">
                            <span class="inline-flex items-center bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full font-semibold gap-1">
                                <i class="fas fa-tag"></i>
                                {{ $product->category->name }} ({{ ucfirst($product->category->type) }})
                            </span>
                        </div>
                        @if($product->description)
                            <div class="mb-1">
                                <h3 class="text-gray-700 font-semibold mb-1">Description</h3>
                                <p class="text-gray-600 leading-relaxed text-sm">{{ $product->description }}</p>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h3 class="text-gray-700 font-semibold mb-2">Ingredients</h3>
                        <div class="overflow-x-auto rounded-lg border">
                            <table class="min-w-full text-xs md:text-sm divide-y divide-gray-200 mb-0">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-500 uppercase">Ingredient</th>
                                        <th class="px-4 py-2 text-left font-semibold text-gray-500 uppercase">Cost</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-100">
                                    @forelse($product->ingredients as $ingredient)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-900">{{ $ingredient->name }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">Rp {{ number_format($ingredient->price, 0, ',', '.') }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="2" class="px-4 py-3 text-center text-gray-400 italic">No ingredients listed</td>
                                    </tr>
                                    @endforelse
                                    <tr class="bg-gray-50 font-bold">
                                        <td class="px-4 py-3">Total Cost</td>
                                        <td class="px-4 py-3">Rp {{ number_format($product->cost_price, 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- card -->
</div>
@endsection