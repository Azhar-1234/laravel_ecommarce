@extends('frontend.layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Shopping Cart</h2>
                
                @if($cartItems->isEmpty())
                    <div class="text-center py-5">
                        <svg width="100" height="100" class="text-muted mb-3">
                            <use xlink:href="#cart"></use>
                        </svg>
                        <h4 class="text-muted">Your cart is empty</h4>
                        <p class="text-muted">Add some products to get started!</p>
                        <a href="{{ route('home') }}" class="btn btn-primary">Continue Shopping</a>
                    </div>
                @else
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-body">
                                    @foreach($cartItems as $item)
                                    <div class="row cart-item" data-item-id="{{ $item->id }}">
                                        <div class="col-md-2">
                                            @if($item->product->image)
                                                <img src="{{ Storage::url($item->product->image) }}" class="img-fluid rounded" alt="{{ $item->product->name }}">
                                            @else
                                                <img src="{{ asset('frontend/assets/images/thumb-bananas.png') }}" class="img-fluid rounded" alt="{{ $item->product->name }}">
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <h5>{{ $item->product->name }}</h5>
                                            <p class="text-muted">{{ Str::limit($item->product->description, 100) }}</p>
                                            <span class="text-success">${{ number_format($item->price, 2) }}</span>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="input-group">
                                                <button class="btn btn-outline-secondary btn-sm quantity-minus" type="button">
                                                    <svg width="16" height="16"><use xlink:href="#minus"></use></svg>
                                                </button>
                                                <input type="number" class="form-control text-center quantity-input" 
                                                       value="{{ $item->quantity }}" 
                                                       min="1" 
                                                       max="{{ $item->product->stock_quantity }}">
                                                <button class="btn btn-outline-secondary btn-sm quantity-plus" type="button">
                                                    <svg width="16" height="16"><use xlink:href="#plus"></use></svg>
                                                </button>
                                            </div>
                                            <small class="text-muted">Stock: {{ $item->product->stock_quantity }}</small>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-end">
                                                <strong class="item-total">${{ number_format($item->total, 2) }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <button class="btn btn-outline-danger btn-sm remove-item" type="button">
                                                <svg width="16" height="16"><use xlink:href="#trash"></use></svg>
                                            </button>
                                        </div>
                                    </div>
                                    @if(!$loop->last)
                                        <hr>
                                    @endif
                                    @endforeach
                                    
                                    <div class="d-flex justify-content-between mt-4">
                                        <a href="{{ route('home') }}" class="btn btn-outline-primary">Continue Shopping</a>
                                        <button class="btn btn-outline-danger clear-cart">Clear Cart</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Order Summary</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal ({{ $cart->total_items }} items)</span>
                                        <span class="cart-subtotal">${{ number_format($cart->total_amount, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Shipping</span>
                                        <span class="text-success">Free</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between mb-3">
                                        <strong>Total</strong>
                                        <strong class="cart-total">${{ number_format($cart->total_amount, 2) }}</strong>
                                    </div>
                                    
                                    @auth
                                        <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100">Proceed to Checkout</a>
                                    @else
                                        <p class="text-muted small mb-3">Please login to proceed with checkout</p>
                                        <a href="{{ route('login') }}" class="btn btn-primary w-100">Login to Checkout</a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
$(document).ready(function() {
    // Quantity controls
    $('.quantity-plus').click(function() {
        let input = $(this).siblings('.quantity-input');
        let max = parseInt(input.attr('max'));
        let current = parseInt(input.val());
        
        if (current < max) {
            input.val(current + 1);
            updateCartItem($(this).closest('.cart-item'));
        }
    });

    $('.quantity-minus').click(function() {
        let input = $(this).siblings('.quantity-input');
        let current = parseInt(input.val());
        
        if (current > 1) {
            input.val(current - 1);
            updateCartItem($(this).closest('.cart-item'));
        }
    });

    $('.quantity-input').change(function() {
        let min = parseInt($(this).attr('min'));
        let max = parseInt($(this).attr('max'));
        let value = parseInt($(this).val());
        
        if (value < min) {
            $(this).val(min);
        } else if (value > max) {
            $(this).val(max);
        }
        
        updateCartItem($(this).closest('.cart-item'));
    });

    // Remove item
    $('.remove-item').click(function() {
        if (confirm('Are you sure you want to remove this item?')) {
            removeCartItem($(this).closest('.cart-item'));
        }
    });

    // Clear cart
    $('.clear-cart').click(function() {
        if (confirm('Are you sure you want to clear your cart?')) {
            clearCart();
        }
    });

    function updateCartItem(cartItem) {
        let itemId = cartItem.data('item-id');
        let quantity = cartItem.find('.quantity-input').val();
        
        $.ajax({
            url: '{{ route("cart.update", ":id") }}'.replace(':id', itemId),
            method: 'PUT',
            data: {
                quantity: quantity,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    cartItem.find('.item-total').text('$' + parseFloat(response.item_total).toFixed(2));
                    $('.cart-subtotal, .cart-total').text('$' + parseFloat(response.cart_total).toFixed(2));
                    updateCartBadge(response.cart_items_count);
                }
            },
            error: function(xhr) {
                let response = xhr.responseJSON;
                alert(response.message || 'Error updating cart');
            }
        });
    }

    function removeCartItem(cartItem) {
        let itemId = cartItem.data('item-id');
        
        $.ajax({
            url: '{{ route("cart.remove", ":id") }}'.replace(':id', itemId),
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    cartItem.fadeOut(300, function() {
                        $(this).remove();
                        $('.cart-subtotal, .cart-total').text('$' + parseFloat(response.cart_total).toFixed(2));
                        updateCartBadge(response.cart_items_count);
                        
                        if (response.cart_items_count === 0) {
                            location.reload();
                        }
                    });
                }
            },
            error: function(xhr) {
                let response = xhr.responseJSON;
                alert(response.message || 'Error removing item');
            }
        });
    }

    function clearCart() {
        $.ajax({
            url: '{{ route("cart.clear") }}',
            method: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            },
            error: function(xhr) {
                let response = xhr.responseJSON;
                alert(response.message || 'Error clearing cart');
            }
        });
    }

    function updateCartBadge(count) {
        $('.cart-badge').text(count);
    }
});
</script>
@endpush
@endsection
