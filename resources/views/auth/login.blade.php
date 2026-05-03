@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="container mx-auto max-w-md">
    <div class="bg-white rounded-lg shadow-lg p-8 mt-10">
        <h1 class="text-3xl font-bold text-gray-900 mb-6 text-center">Login</h1>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Username
                </label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter your username"
                />
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Password
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter your password"
                />
                @error('password')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200"
            >
                Login
            </button>
        </form>

        <p class="text-center text-gray-600 mt-6">
            Don't have an account? <a href="{{ route('register') }}" class="text-blue-600 hover:underline font-semibold">Create one</a>
        </p>
    </div>
</div>
@endsection
