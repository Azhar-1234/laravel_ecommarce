@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Product Details</h1>
            <div class="space-x-2">
                <a href="{{ route('products.edit', $product) }}" 
                   class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Edit
                </a>
                <a href="{{ route('products.index') }}" 
                   class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    Back to Products
                </a>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6">
                <!-- Product Image -->
                <div>
                    @if($product->image)
                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" 
                             class="w-full h-96 object-cover rounded-lg">
                    @else
                        <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center">
                            <span class="text-gray-400 text-xl">No Image Available</span>
                        </div>
                    @endif
                </div>

                <!-- Product Information -->
                <div class="space-y-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h2>
                        <p class="text-gray-600">{{ $product->slug }}</p>
                    </div>

                    <div>
                        <span class="text-3xl font-bold text-green-600">${{ number_format($product->price, 2) }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-700">Stock Quantity</h3>
                            <p class="mt-1">
                                <span class="px-2 py-1 text-sm font-semibold rounded-full 
                                    {{ $product->stock_quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->stock_quantity }} {{ $product->stock_quantity == 1 ? 'item' : 'items' }}
                                </span>
                            </p>
                        </div>

                        <div>
                            <h3 class="text-sm font-medium text-gray-700">Status</h3>
                            <p class="mt-1">
                                <span class="px-2 py-1 text-sm font-semibold rounded-full 
                                    {{ $product->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $product->status ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                    </div>

                    @if($product->category)
                        <div>
                            <h3 class="text-sm font-medium text-gray-700">Category</h3>
                            <p class="mt-1 text-sm text-gray-900">{{ $product->category }}</p>
                        </div>
                    @endif

                    <div>
                        <h3 class="text-sm font-medium text-gray-700">Created</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-700">Last Updated</h3>
                        <p class="mt-1 text-sm text-gray-900">{{ $product->updated_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            </div>

            @if($product->description)
                <div class="border-t border-gray-200 px-6 py-4">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                    <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="border-t border-gray-200 px-6 py-4 bg-gray-50">
                <div class="flex justify-between items-center">
                    <form action="{{ route('products.toggle-status', $product) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" 
                                class="px-4 py-2 font-semibold rounded 
                                {{ $product->status ? 'bg-red-500 hover:bg-red-700 text-white' : 'bg-green-500 hover:bg-green-700 text-white' }}">
                            {{ $product->status ? 'Deactivate' : 'Activate' }}
                        </button>
                    </form>

                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                            Delete Product
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
