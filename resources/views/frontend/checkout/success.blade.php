@extends('frontend.layouts.app')

@section('title', 'Order Confirmation')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center mb-5">
                    <div class="text-success mb-3">
                        <svg width="80" height="80" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <h2 class="text-success">Order Confirmed!</h2>
                    <p class="lead">Thank you for your order. We've received your order and will process it shortly.</p>
                    <p class="text-muted">Order Number: <strong>{{ $order->order_number }}</strong></p>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Order Number:</strong></div>
                            <div class="col-sm-9">{{ $order->order_number }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Order Date:</strong></div>
                            <div class="col-sm-9">{{ $order->created_at->format('F j, Y \a\t g:i A') }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Total Amount:</strong></div>
                            <div class="col-sm-9">${{ number_format($order->total_amount, 2) }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Payment Method:</strong></div>
                            <div class="col-sm-9">{{ ucwords(str_replace('_', ' ', $order->payment_method)) }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Status:</strong></div>
                            <div class="col-sm-9">
                                <span class="badge bg-warning">{{ ucfirst($order->status) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Items Ordered</h5>
                    </div>
                    <div class="card-body">
                        @foreach($order->items as $item)
                        <div class="row align-items-center mb-3">
                            <div class="col-md-2">
                                @if($item->product && $item->product->image)
                                    <img src="{{ Storage::url($item->product->image) }}" class="img-fluid rounded" alt="{{ $item->product_name }}">
                                @else
                                    <img src="{{ asset('frontend/assets/images/thumb-bananas.png') }}" class="img-fluid rounded" alt="{{ $item->product_name }}">
                                @endif
                            </div>
                            <div class="col-md-6">
                                <h6>{{ $item->product_name }}</h6>
                                <p class="text-muted mb-0">Quantity: {{ $item->quantity }}</p>
                                <p class="text-muted mb-0">Price: ${{ number_format($item->price, 2) }}</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <strong>${{ number_format($item->total, 2) }}</strong>
                            </div>
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                        @endforeach
                        
                        <hr>
                        <div class="row">
                            <div class="col-md-8"></div>
                            <div class="col-md-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span>${{ number_format($order->total_amount, 2) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping:</span>
                                    <span class="text-success">Free</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>Total:</strong>
                                    <strong>${{ number_format($order->total_amount, 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Billing Address</h5>
                            </div>
                            <div class="card-body">
                                <address>
                                    <strong>{{ $order->billing_first_name }} {{ $order->billing_last_name }}</strong><br>
                                    {{ $order->billing_address }}<br>
                                    {{ $order->billing_city }}, {{ $order->billing_state }} {{ $order->billing_postal_code }}<br>
                                    {{ $order->billing_country }}<br>
                                    @if($order->billing_phone)
                                        Phone: {{ $order->billing_phone }}<br>
                                    @endif
                                    Email: {{ $order->billing_email }}
                                </address>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Shipping Address</h5>
                            </div>
                            <div class="card-body">
                                <address>
                                    <strong>{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}</strong><br>
                                    {{ $order->shipping_address }}<br>
                                    {{ $order->shipping_city }}, {{ $order->shipping_state }} {{ $order->shipping_postal_code }}<br>
                                    {{ $order->shipping_country }}
                                </address>
                            </div>
                        </div>
                    </div>
                </div>

                @if($order->notes)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Notes</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $order->notes }}</p>
                    </div>
                </div>
                @endif

                <div class="text-center mt-5">
                    <a href="{{ route('home') }}" class="btn btn-primary me-3">Continue Shopping</a>
                    <button onclick="window.print()" class="btn btn-outline-secondary">Print Order</button>
                </div>
            </div>
        </div>
    </div>
</section>

@push('styles')
<style>
@media print {
    .btn, .navbar, .footer {
        display: none !important;
    }
}
</style>
@endpush
@endsection
