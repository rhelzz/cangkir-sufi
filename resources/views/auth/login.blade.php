@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="bg-gray-800 text-white p-6 text-center">
        <h3 class="text-xl font-bold mb-1"><i class="fas fa-cash-register mr-2"></i> POS Mobile</h3>
        <p class="text-gray-300">Login to your account</p>
    </div>
    <div class="p-6">
        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">Email Address</label>
                <input type="email" 
                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('email') border-red-500 @enderror" 
                    id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" 
                    class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 @error('password') border-red-500 @enderror" 
                    id="password" name="password" required>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" class="form-checkbox h-4 w-4 text-blue-600" id="remember" name="remember" 
                        {{ old('remember') ? 'checked' : '' }}>
                    <span class="ml-2 text-gray-700 text-sm">Remember Me</span>
                </label>
            </div>
            
            <button type="submit" class="w-full bg-gray-800 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-md transition-colors duration-200 flex items-center justify-center">
                <i class="fas fa-sign-in-alt mr-2"></i> Login
            </button>
        </form>
    </div>
</div>
@endsection