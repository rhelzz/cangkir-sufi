@extends('layouts.app')

@section('title', 'Create Category')
@section('page-title', 'Create Category')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 p-5 border-b bg-gradient-to-r from-blue-50 to-white">
            <h2 class="text-xl font-bold flex items-center gap-2">
                <i class="fas fa-layer-group text-blue-400"></i>
                Create New Category
            </h2>
            <a href="{{ route('categories.index') }}" class="inline-flex items-center bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm shadow transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>
        
        <div class="p-4 md:p-6">
            <form action="{{ route('categories.store') }}" method="POST" autocomplete="off">
                @csrf
                
                <div class="mb-5">
                    <label for="name" class="block text-gray-700 text-sm font-bold mb-2">Category Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" 
                        class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror" required autofocus>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Type <span class="text-red-500">*</span></label>
                    <div class="flex space-x-3">
                        <label class="flex items-center cursor-pointer bg-red-50 border border-red-200 rounded-md px-3 py-2 transition hover:bg-red-100 shadow-sm {{ old('type') == 'hot' ? 'ring-2 ring-red-400' : '' }}">
                            <input type="radio" name="type" value="hot" {{ old('type') == 'hot' ? 'checked' : '' }} 
                                class="form-radio h-4 w-4 text-red-600 focus:ring-red-500" required>
                            <span class="ml-2 text-red-700 font-semibold flex items-center gap-1">
                                <i class="fas fa-mug-hot"></i>
                                Hot
                            </span>
                        </label>
                        <label class="flex items-center cursor-pointer bg-blue-50 border border-blue-200 rounded-md px-3 py-2 transition hover:bg-blue-100 shadow-sm {{ old('type') == 'cold' ? 'ring-2 ring-blue-400' : '' }}">
                            <input type="radio" name="type" value="cold" {{ old('type') == 'cold' ? 'checked' : '' }} 
                                class="form-radio h-4 w-4 text-blue-600 focus:ring-blue-500" required>
                            <span class="ml-2 text-blue-700 font-semibold flex items-center gap-1">
                                <i class="fas fa-ice-cream"></i>
                                Cold
                            </span>
                        </label>
                    </div>
                    @error('type')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end mt-8">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-semibold shadow transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i> Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection