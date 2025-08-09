@extends('frontend.layouts.app')

@section('title', 'Checkout')

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Checkout</h2>
                
                <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                    @csrf
                    <div class="row">
                        <div class="col-lg-7">
                            <!-- Billing Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Billing Information</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="billing_first_name" class="form-label">First Name *</label>
                                            <input type="text" class="form-control @error('billing_first_name') is-invalid @enderror" 
                                                   id="billing_first_name" name="billing_first_name" 
                                                   value="{{ old('billing_first_name', auth()->user()->name ?? '') }}" required>
                                            @error('billing_first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="billing_last_name" class="form-label">Last Name *</label>
                                            <input type="text" class="form-control @error('billing_last_name') is-invalid @enderror" 
                                                   id="billing_last_name" name="billing_last_name" 
                                                   value="{{ old('billing_last_name') }}" required>
                                            @error('billing_last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="billing_email" class="form-label">Email *</label>
                                            <input type="email" class="form-control @error('billing_email') is-invalid @enderror" 
                                                   id="billing_email" name="billing_email" 
                                                   value="{{ old('billing_email', auth()->user()->email ?? '') }}" required>
                                            @error('billing_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="billing_phone" class="form-label">Phone</label>
                                            <input type="tel" class="form-control @error('billing_phone') is-invalid @enderror" 
                                                   id="billing_phone" name="billing_phone" 
                                                   value="{{ old('billing_phone') }}">
                                            @error('billing_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="billing_address" class="form-label">Address *</label>
                                        <input type="text" class="form-control @error('billing_address') is-invalid @enderror" 
                                               id="billing_address" name="billing_address" 
                                               value="{{ old('billing_address') }}" required>
                                        @error('billing_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="billing_city" class="form-label">City *</label>
                                            <input type="text" class="form-control @error('billing_city') is-invalid @enderror" 
                                                   id="billing_city" name="billing_city" 
                                                   value="{{ old('billing_city') }}" required>
                                            @error('billing_city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="billing_state" class="form-label">State *</label>
                                            <input type="text" class="form-control @error('billing_state') is-invalid @enderror" 
                                                   id="billing_state" name="billing_state" 
                                                   value="{{ old('billing_state') }}" required>
                                            @error('billing_state')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="billing_postal_code" class="form-label">Postal Code *</label>
                                            <input type="text" class="form-control @error('billing_postal_code') is-invalid @enderror" 
                                                   id="billing_postal_code" name="billing_postal_code" 
                                                   value="{{ old('billing_postal_code') }}" required>
                                            @error('billing_postal_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="billing_country" class="form-label">Country *</label>
                                            <select class="form-select @error('billing_country') is-invalid @enderror" 
                                                    id="billing_country" name="billing_country" required>
                                                <option value="">Select Country</option>
                                                <option value="US" {{ old('billing_country') == 'US' ? 'selected' : '' }}>United States</option>
                                                <option value="CA" {{ old('billing_country') == 'CA' ? 'selected' : '' }}>Canada</option>
                                                <option value="UK" {{ old('billing_country') == 'UK' ? 'selected' : '' }}>United Kingdom</option>
                                                <option value="AU" {{ old('billing_country') == 'AU' ? 'selected' : '' }}>Australia</option>
                                                <!-- Add more countries as needed -->
                                            </select>
                                            @error('billing_country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Shipping Information -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0">Shipping Information</h5>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="same_as_billing">
                                        <label class="form-check-label" for="same_as_billing">
                                            Same as billing
                                        </label>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="shipping_first_name" class="form-label">First Name *</label>
                                            <input type="text" class="form-control @error('shipping_first_name') is-invalid @enderror" 
                                                   id="shipping_first_name" name="shipping_first_name" 
                                                   value="{{ old('shipping_first_name') }}" required>
                                            @error('shipping_first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="shipping_last_name" class="form-label">Last Name *</label>
                                            <input type="text" class="form-control @error('shipping_last_name') is-invalid @enderror" 
                                                   id="shipping_last_name" name="shipping_last_name" 
                                                   value="{{ old('shipping_last_name') }}" required>
                                            @error('shipping_last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="shipping_address" class="form-label">Address *</label>
                                        <input type="text" class="form-control @error('shipping_address') is-invalid @enderror" 
                                               id="shipping_address" name="shipping_address" 
                                               value="{{ old('shipping_address') }}" required>
                                        @error('shipping_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="shipping_city" class="form-label">City *</label>
                                            <input type="text" class="form-control @error('shipping_city') is-invalid @enderror" 
                                                   id="shipping_city" name="shipping_city" 
                                                   value="{{ old('shipping_city') }}" required>
                                            @error('shipping_city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="shipping_state" class="form-label">State *</label>
                                            <input type="text" class="form-control @error('shipping_state') is-invalid @enderror" 
                                                   id="shipping_state" name="shipping_state" 
                                                   value="{{ old('shipping_state') }}" required>
                                            @error('shipping_state')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="shipping_postal_code" class="form-label">Postal Code *</label>
                                            <input type="text" class="form-control @error('shipping_postal_code') is-invalid @enderror" 
                                                   id="shipping_postal_code" name="shipping_postal_code" 
                                                   value="{{ old('shipping_postal_code') }}" required>
                                            @error('shipping_postal_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="shipping_country" class="form-label">Country *</label>
                                            <select class="form-select @error('shipping_country') is-invalid @enderror" 
                                                    id="shipping_country" name="shipping_country" required>
                                                <option value="">Select Country</option>
                                                <option value="US" {{ old('shipping_country') == 'US' ? 'selected' : '' }}>United States</option>
                                                <option value="CA" {{ old('shipping_country') == 'CA' ? 'selected' : '' }}>Canada</option>
                                                <option value="UK" {{ old('shipping_country') == 'UK' ? 'selected' : '' }}>United Kingdom</option>
                                                <option value="AU" {{ old('shipping_country') == 'AU' ? 'selected' : '' }}>Australia</option>
                                                <!-- Add more countries as needed -->
                                            </select>
                                            @error('shipping_country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Payment Method</h5>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-3">
                                        <input class="form-check-input @error('payment_method') is-invalid @enderror" 
                                               type="radio" name="payment_method" id="cod" value="cash_on_delivery" 
                                               {{ old('payment_method') == 'cash_on_delivery' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="cod">
                                            Cash on Delivery
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input @error('payment_method') is-invalid @enderror" 
                                               type="radio" name="payment_method" id="card" value="card" 
                                               {{ old('payment_method') == 'card' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="card">
                                            Credit/Debit Card
                                        </label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input class="form-check-input @error('payment_method') is-invalid @enderror" 
                                               type="radio" name="payment_method" id="paypal" value="paypal" 
                                               {{ old('payment_method') == 'paypal' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="paypal">
                                            PayPal
                                        </label>
                                    </div>
                                    @error('payment_method')
                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Order Notes -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">Order Notes (Optional)</h5>
                                </div>
                                <div class="card-body">
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              name="notes" rows="3" 
                                              placeholder="Any special instructions for your order...">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-5">
                            <!-- Order Summary -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Order Summary</h5>
                                </div>
                                <div class="card-body">
                                    @foreach($cartItems as $item)
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center">
                                            @if($item->product->image)
                                                <img src="{{ Storage::url($item->product->image) }}" class="me-3" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $item->product->name }}">
                                            @else
                                                <img src="{{ asset('frontend/assets/images/thumb-bananas.png') }}" class="me-3" style="width: 50px; height: 50px; object-fit: cover;" alt="{{ $item->product->name }}">
                                            @endif
                                            <div>
                                                <h6 class="mb-0">{{ $item->product->name }}</h6>
                                                <small class="text-muted">Qty: {{ $item->quantity }}</small>
                                            </div>
                                        </div>
                                        <span>${{ number_format($item->total, 2) }}</span>
                                    </div>
                                    @endforeach
                                    
                                    <hr>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Subtotal</span>
                                        <span>${{ number_format($cart->total_amount, 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Shipping</span>
                                        <span class="text-success">Free</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between mb-3">
                                        <strong>Total</strong>
                                        <strong>${{ number_format($cart->total_amount, 2) }}</strong>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100">Place Order</button>
                                    <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary w-100 mt-2">Back to Cart</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@push('scripts')
<script>
$(document).ready(function() {
    // Copy billing to shipping
    $('#same_as_billing').change(function() {
        if ($(this).is(':checked')) {
            $('#shipping_first_name').val($('#billing_first_name').val());
            $('#shipping_last_name').val($('#billing_last_name').val());
            $('#shipping_address').val($('#billing_address').val());
            $('#shipping_city').val($('#billing_city').val());
            $('#shipping_state').val($('#billing_state').val());
            $('#shipping_postal_code').val($('#billing_postal_code').val());
            $('#shipping_country').val($('#billing_country').val());
        } else {
            $('#shipping_first_name, #shipping_last_name, #shipping_address, #shipping_city, #shipping_state, #shipping_postal_code').val('');
            $('#shipping_country').val('');
        }
    });

    // Auto-copy when billing fields change and checkbox is checked
    $('#billing_first_name, #billing_last_name, #billing_address, #billing_city, #billing_state, #billing_postal_code, #billing_country').change(function() {
        if ($('#same_as_billing').is(':checked')) {
            let fieldName = $(this).attr('id').replace('billing_', 'shipping_');
            $('#' + fieldName).val($(this).val());
        }
    });

    // Form submission
    $('#checkout-form').submit(function(e) {
        // Basic validation
        if (!$('input[name="payment_method"]:checked').length) {
            e.preventDefault();
            alert('Please select a payment method.');
            return false;
        }
        
        // Show loading state
        $(this).find('button[type="submit"]').prop('disabled', true).text('Processing...');
    });
});
</script>
@endpush
@endsection
