<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;


Route::get('/', [HomeController::class, 'index'])->name('home');

// Cart routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::put('/update/{id}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('remove');
    Route::delete('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/data', [CartController::class, 'getCartData'])->name('data');
});

// Checkout routes
Route::prefix('checkout')->name('checkout.')->middleware('auth')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/', [CheckoutController::class, 'store'])->name('store');
    Route::get('/success', [CheckoutController::class, 'success'])->name('success');
});

// Test route to check authentication status
Route::get('/test-auth', function () {
    return response()->json([
        'authenticated' => auth()->check(),
        'user' => auth()->user(),
        'routes' => [
            'login' => route('login'),
            'register' => url('/register'),
        ]
    ]);
});


Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Product routes
    Route::resource('products', ProductController::class);
    Route::patch('products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
});

// Test route for email functionality
Route::get('/test-email', function () {
    try {
        // Get a product with success_mail
        $product = \App\Models\Product::where('success_mail', '!=', null)->first();
        
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'No product found with success_mail field'
            ]);
        }
        
        // Create a test order
        $order = new \App\Models\Order();
        $order->id = 999; // Test ID
        $order->total_amount = 25.50;
        $order->billing_email = 'customer@test.com';
        $order->shipping_first_name = 'John';
        $order->shipping_last_name = 'Doe';
        $order->billing_phone = '+1234567890';
        $order->shipping_address = '123 Test Street';
        $order->shipping_city = 'Test City';
        $order->shipping_state = 'Test State';
        $order->shipping_postal_code = '12345';
        $order->shipping_country = 'Test Country';
        $order->created_at = now();
        
        // Prepare customer data
        $order->customer_name = $order->shipping_first_name . ' ' . $order->shipping_last_name;
        $order->customer_email = $order->billing_email;
        $order->customer_phone = $order->billing_phone;
        $order->shipping_address = $order->shipping_address . ', ' . $order->shipping_city . ', ' . $order->shipping_state . ' ' . $order->shipping_postal_code . ', ' . $order->shipping_country;
        
        // Create test order items
        $orderItems = collect([
            (object) [
                'product' => $product,
                'quantity' => 2,
                'price' => 12.99
            ]
        ]);
        
        // Determine email address
        $emailAddress = $product->success_mail ?: $order->billing_email;
        
        // Check for attachment
        $attachmentPath = null;
        if ($product->attachment) {
            $filePath = storage_path('app/public/' . $product->attachment);
            if (file_exists($filePath)) {
                $attachmentPath = $filePath;
            }
        }
        
        // Send email
        \Illuminate\Support\Facades\Mail::to($emailAddress)->send(new \App\Mail\OrderSuccessMail($order, $attachmentPath));
        
        return response()->json([
            'status' => 'success',
            'message' => "Test email sent successfully!",
            'details' => [
                'email_sent_to' => $emailAddress,
                'product_name' => $product->name,
                'has_attachment' => !is_null($attachmentPath),
                'attachment_path' => $product->attachment,
                'success_mail' => $product->success_mail
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to send test email: ' . $e->getMessage()
        ]);
    }
});

// Test route for complete order simulation
Route::get('/test-order', function () {
    try {
        // Get a product with success_mail and attachment
        $product = \App\Models\Product::where('success_mail', '!=', null)->first();
        
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'No product found with success_mail field'
            ]);
        }
        
        // Create a real order in the database
        $order = \App\Models\Order::create([
            'order_number' => \App\Models\Order::generateOrderNumber(),
            'user_id' => null, // Guest order
            'total_amount' => $product->price * 2,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cash_on_delivery',
            'billing_first_name' => 'John',
            'billing_last_name' => 'Doe',
            'billing_email' => 'customer@test.com',
            'billing_phone' => '+1234567890',
            'billing_address' => '123 Test Street',
            'billing_city' => 'Test City',
            'billing_state' => 'Test State',
            'billing_postal_code' => '12345',
            'billing_country' => 'Test Country',
            'shipping_first_name' => 'John',
            'shipping_last_name' => 'Doe',
            'shipping_address' => '123 Test Street',
            'shipping_city' => 'Test City',
            'shipping_state' => 'Test State',
            'shipping_postal_code' => '12345',
            'shipping_country' => 'Test Country',
            'notes' => 'Test order for email functionality',
        ]);
        
        // Create order item
        \App\Models\OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_name' => $product->name,
            'quantity' => 2,
            'price' => $product->price,
            'total' => $product->price * 2,
        ]);
        
        // Simulate cart items for email
        $cartItems = collect([
            (object) [
                'product' => $product,
                'quantity' => 2,
                'price' => $product->price
            ]
        ]);
        
        // Prepare customer data for email
        $order->customer_name = $order->shipping_first_name . ' ' . $order->shipping_last_name;
        $order->customer_email = $order->billing_email;
        $order->customer_phone = $order->billing_phone;
        $order->shipping_address = $order->shipping_address . ', ' . $order->shipping_city . ', ' . $order->shipping_state . ' ' . $order->shipping_postal_code . ', ' . $order->shipping_country;
        
        // Determine email address - use success_mail if available
        $emailAddress = $product->success_mail ?: $order->billing_email;
        
        // Check for attachment
        $attachmentPath = null;
        if ($product->attachment) {
            $filePath = storage_path('app/public/' . $product->attachment);
            if (file_exists($filePath)) {
                $attachmentPath = $filePath;
            }
        }
        
        // Send email using the same logic as CheckoutController
        \Illuminate\Support\Facades\Mail::to($emailAddress)->send(new \App\Mail\OrderSuccessMail($order, $attachmentPath));
        
        return response()->json([
            'status' => 'success',
            'message' => "Complete order test successful!",
            'order_details' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total_amount' => $order->total_amount,
                'email_sent_to' => $emailAddress,
                'product_name' => $product->name,
                'success_mail' => $product->success_mail,
                'attachment' => $product->attachment,
                'has_attachment' => !is_null($attachmentPath),
                'attachment_exists' => $attachmentPath ? file_exists($attachmentPath) : false
            ]
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create test order: ' . $e->getMessage()
        ]);
    }
});

require __DIR__.'/auth.php';
