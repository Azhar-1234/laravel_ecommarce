<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use App\Mail\OrderSuccessMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cartData = $this->cartService->getCartData();
        $cart = $cartData['cart'];
        $cartItems = $cartData['items'];
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }
        
        return view('frontend.checkout.index', compact('cart', 'cartItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'billing_first_name' => 'required|string|max:255',
            'billing_last_name' => 'required|string|max:255',
            'billing_email' => 'required|email|max:255',
            'billing_phone' => 'nullable|string|max:20',
            'billing_address' => 'required|string|max:255',
            'billing_city' => 'required|string|max:255',
            'billing_state' => 'required|string|max:255',
            'billing_postal_code' => 'required|string|max:10',
            'billing_country' => 'required|string|max:255',
            'shipping_first_name' => 'required|string|max:255',
            'shipping_last_name' => 'required|string|max:255',
            'shipping_address' => 'required|string|max:255',
            'shipping_city' => 'required|string|max:255',
            'shipping_state' => 'required|string|max:255',
            'shipping_postal_code' => 'required|string|max:10',
            'shipping_country' => 'required|string|max:255',
            'payment_method' => 'required|string|in:cash_on_delivery,card,paypal',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cartData = $this->cartService->getCartData();
        $cart = $cartData['cart'];
        $cartItems = $cartData['items'];
        
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Check stock availability
        foreach ($cartItems as $item) {
            if ($item->product->stock_quantity < $item->quantity) {
                return back()->with('error', "Insufficient stock for {$item->product->name}. Only {$item->product->stock_quantity} items available.");
            }
        }

        DB::transaction(function () use ($request, $cart, $cartItems, &$order) {
            // Create order
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => Auth::id(),
                'total_amount' => $cart->total_amount,
                'status' => 'pending',
                'payment_status' => $request->payment_method === 'cash_on_delivery' ? 'pending' : 'pending',
                'payment_method' => $request->payment_method,
                'billing_first_name' => $request->billing_first_name,
                'billing_last_name' => $request->billing_last_name,
                'billing_email' => $request->billing_email,
                'billing_phone' => $request->billing_phone,
                'billing_address' => $request->billing_address,
                'billing_city' => $request->billing_city,
                'billing_state' => $request->billing_state,
                'billing_postal_code' => $request->billing_postal_code,
                'billing_country' => $request->billing_country,
                'shipping_first_name' => $request->shipping_first_name,
                'shipping_last_name' => $request->shipping_last_name,
                'shipping_address' => $request->shipping_address,
                'shipping_city' => $request->shipping_city,
                'shipping_state' => $request->shipping_state,
                'shipping_postal_code' => $request->shipping_postal_code,
                'shipping_country' => $request->shipping_country,
                'notes' => $request->notes,
            ]);

            // Create order items and update stock
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->quantity * $item->price,
                ]);

                // Update product stock
                $item->product->decrement('stock_quantity', $item->quantity);
            }

            // Clear cart
            $this->cartService->clearCart();

            session(['order_id' => $order->id]);
        });

        // Send order success email
        $this->sendOrderSuccessEmail($order, $cartItems);

        return redirect()->route('checkout.success');
    }

    public function success()
    {
        $orderId = session('order_id');
        
        if (!$orderId) {
            return redirect()->route('home');
        }

        $order = Order::with('items.product')->findOrFail($orderId);
        
        // Clear the order from session
        session()->forget('order_id');
        
        return view('frontend.checkout.success', compact('order'));
    }

    /**
     * Send order success email with attachment if available
     */
    private function sendOrderSuccessEmail($order, $cartItems)
    {
        try {
            // Prepare customer data for the email
            $order->customer_name = $order->shipping_first_name . ' ' . $order->shipping_last_name;
            $order->customer_email = $order->billing_email;
            $order->customer_phone = $order->billing_phone;
            $order->shipping_address = $order->shipping_address . ', ' . $order->shipping_city . ', ' . $order->shipping_state . ' ' . $order->shipping_postal_code . ', ' . $order->shipping_country;
            
            // Look for attachment from products (if exists)
            $attachmentPath = null;
            foreach ($cartItems as $item) {

                if ($item->product && $item->product->attachment) {
                    // Check if attachment file exists in storage
                    $filePath = storage_path('app/public/' . $item->product->attachment);
                    if (file_exists($filePath)) {
                        $attachmentPath = $filePath;
                        break; // Use the first available attachment
                    }
                }
            }
            
            // Email will always be sent to customer's billing email
            $emailAddress = $order->billing_email;
            
            // Get success message from product if available
            $successMessage = null;
            foreach ($cartItems as $item) {
                if ($item->product && !empty($item->product->success_mail)) {
                    $successMessage = $item->product->success_mail;
                    break; // Use the first available success_mail message
                }
            }
            
            // Send email with success message and attachment
            Mail::to($emailAddress)->send(new OrderSuccessMail($order, $attachmentPath, $successMessage));
            
            // Log successful email sending
            Log::info("Order success email sent successfully to: {$emailAddress} for Order ID: {$order->id}");
            
        } catch (\Exception $e) {
            // Log the error but don't break the checkout process
            Log::error('Failed to send order success email: ' . $e->getMessage());
        }
    }
}
