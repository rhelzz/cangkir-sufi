@extends('layouts.app')

@section('title', 'Register User')
@section('page-title', 'Register New User')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="flex justify-between items-center px-6 py-4 border-b bg-gradient-to-r from-blue-50 to-white">
            <h5 class="text-lg font-bold flex items-center gap-2">
                <i class="fas fa-user-plus text-blue-500"></i>
                Add New User
            </h5>
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1 bg-gray-200 hover:bg-gray-300 text-gray-700 py-1.5 px-3 rounded-md text-sm font-semibold shadow-sm transition-colors duration-200">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="p-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-2 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
            <form method="POST" action="{{ route('register') }}" autocomplete="off" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-gray-700 text-sm font-semibold mb-1">Name</label>
                    <div class="relative">
                        <input type="text" 
                            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('name') border-red-500 @enderror transition"
                            id="name" name="name" value="{{ old('name') }}" required autofocus
                            placeholder="Enter user's full name">
                        <span class="absolute inset-y-0 right-3 flex items-center text-gray-400 pointer-events-none">
                            <i class="fas fa-user"></i>
                        </span>
                    </div>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-gray-700 text-sm font-semibold mb-1">Email Address</label>
                    <div class="relative">
                        <input type="email" 
                            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror transition"
                            id="email" name="email" value="{{ old('email') }}" required
                            placeholder="Enter user's email">
                        <span class="absolute inset-y-0 right-3 flex items-center text-gray-400 pointer-events-none">
                            <i class="fas fa-envelope"></i>
                        </span>
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-gray-700 text-sm font-semibold mb-1">Password</label>
                    <div class="relative">
                        <input type="password" 
                            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror transition"
                            id="password" name="password" required
                            placeholder="Create a password">
                        <span class="absolute inset-y-0 right-3 flex items-center text-gray-400 pointer-events-none">
                            <i class="fas fa-lock"></i>
                        </span>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <div class="text-xs text-gray-500 mt-1">
                        At least 8 characters, contains uppercase, lowercase, and number.
                    </div>
                </div>

                <div>
                    <label for="password-confirm" class="block text-gray-700 text-sm font-semibold mb-1">Confirm Password</label>
                    <div class="relative">
                        <input type="password"
                            class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                            id="password-confirm" name="password_confirmation" required
                            placeholder="Repeat password">
                        <span class="absolute inset-y-0 right-3 flex items-center text-gray-400 pointer-events-none">
                            <i class="fas fa-lock"></i>
                        </span>
                    </div>
                </div>

                <div>
                    <label for="role" class="block text-gray-700 text-sm font-semibold mb-1">Role</label>
                    <div class="relative">
                        <select class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('role') border-red-500 @enderror transition appearance-none"
                            id="role" name="role" required>
                            <option value="">Select Role</option>
                            <option value="cashier" {{ old('role') == 'cashier' ? 'selected' : '' }}>Cashier</option>
                            <option value="owner" {{ old('role') == 'owner' ? 'selected' : '' }}>Owner</option>
                        </select>
                        <span class="absolute inset-y-0 right-3 flex items-center text-gray-400 pointer-events-none">
                            <i class="fas fa-user-tag"></i>
                        </span>
                    </div>
                    @error('role')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full flex justify-center items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-md shadow transition-colors duration-200 text-lg">
                    <i class="fas fa-user-plus"></i> Register User
                </button>
            </form>
        </div>
    </div>
</div>
@endsection