<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    /**
     * Get or create cart for current user/session
     */
    public function getOrCreateCart()
    {
        if (Auth::check()) {
            return Cart::firstOrCreate([
                'user_id' => Auth::id()
            ], [
                'session_id' => null
            ]);
        } else {
            $sessionId = Session::getId();
            return Cart::firstOrCreate([
                'session_id' => $sessionId,
                'user_id' => null
            ]);
        }
    }

    /**
     * Add product to cart
     */
    public function addToCart($productId, $quantity)
    {
        $product = Product::findOrFail($productId);
        
        // Check stock
        if ($product->stock_quantity < $quantity) {
            throw new \Exception('Insufficient stock. Only ' . $product->stock_quantity . ' items available.');
        }

        $cart = $this->getOrCreateCart();
        
        // Check if item already exists
        $cartItem = $cart->items()->where('product_id', $productId)->first();
        
        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $quantity;
            
            if ($product->stock_quantity < $newQuantity) {
                throw new \Exception('Cannot add more items. Total quantity would exceed available stock.');
            }
            
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cartItem = $cart->items()->create([
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);
        }

        return $cartItem;
    }

    /**
     * Update cart item quantity
     */
    public function updateCartItem($cartItemId, $quantity)
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        $cart = $this->getOrCreateCart();
        
        if ($cartItem->cart_id !== $cart->id) {
            throw new \Exception('Unauthorized access to cart item.');
        }

        if ($cartItem->product->stock_quantity < $quantity) {
            throw new \Exception('Insufficient stock. Only ' . $cartItem->product->stock_quantity . ' items available.');
        }

        $cartItem->update(['quantity' => $quantity]);
        
        return $cartItem;
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart($cartItemId)
    {
        $cartItem = CartItem::findOrFail($cartItemId);
        $cart = $this->getOrCreateCart();
        
        if ($cartItem->cart_id !== $cart->id) {
            throw new \Exception('Unauthorized access to cart item.');
        }

        $cartItem->delete();
        
        return true;
    }

    /**
     * Clear cart
     */
    public function clearCart()
    {
        $cart = $this->getOrCreateCart();
        $cart->items()->delete();
        
        return true;
    }

    /**
     * Get cart data
     */
    public function getCartData()
    {
        $cart = $this->getOrCreateCart();
        
        return [
            'cart' => $cart,
            'items' => $cart->items()->with('product')->get(),
            'total_amount' => $cart->total_amount,
            'total_items' => $cart->total_items,
        ];
    }

    /**
     * Transfer guest cart to user cart after login
     */
    public function transferGuestCartToUser($userId)
    {
        $sessionId = Session::getId();
        $guestCart = Cart::where('session_id', $sessionId)->whereNull('user_id')->first();
        
        if ($guestCart) {
            $userCart = Cart::firstOrCreate([
                'user_id' => $userId
            ], [
                'session_id' => null
            ]);
            
            // Transfer items
            foreach ($guestCart->items as $guestItem) {
                $existingItem = $userCart->items()->where('product_id', $guestItem->product_id)->first();
                
                if ($existingItem) {
                    $existingItem->increment('quantity', $guestItem->quantity);
                } else {
                    $userCart->items()->create([
                        'product_id' => $guestItem->product_id,
                        'quantity' => $guestItem->quantity,
                        'price' => $guestItem->price,
                    ]);
                }
            }
            
            // Delete guest cart
            $guestCart->items()->delete();
            $guestCart->delete();
        }
    }
}
