@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Create New Product</h1>
            <a href="{{ route('products.index') }}" 
               class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                Back to Products
            </a>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" id="description" rows="4"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                        <input type="number" name="price" id="price" value="{{ old('price') }}" step="0.01" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity *</label>
                        <input type="number" name="stock_quantity" id="stock_quantity" value="{{ old('stock_quantity', 0) }}" min="0" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <input type="text" name="category" id="category" value="{{ old('category') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="mb-4">
                    <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Product Image</label>
                    <input type="file" name="image" id="image" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Supported formats: JPEG, PNG, JPG, GIF. Max size: 2MB</p>
                </div>
                <div class="mb-4">
                    <label for="attachment" class="block text-sm font-medium text-gray-700 mb-2">Attachment (optional)</label>
                    <input type="file" name="attachment" id="attachment" accept=".pdf,.doc,.docx"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Supported formats: PDF, DOC, DOCX. Max size: 2MB</p>
                </div>
                <div class="mb-6">
                    <label for="success_mail" class="block text-sm font-medium text-gray-700 mb-2">Success Mail (optional)</label>
                    <input type="text" name="success_mail" id="success_mail" value="{{ old('success_mail') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Enter an email address to receive a success notification.</p>
                </div>
                </div>
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="status" id="status" value="1" {{ old('status', true) ? 'checked' : '' }}
                               class="mr-2 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>

                <div class="flex justify-end space-x-4">
                    <a href="{{ route('products.index') }}" 
                       class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Create Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
