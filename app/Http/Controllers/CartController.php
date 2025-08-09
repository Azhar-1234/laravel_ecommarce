<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;
use Exception;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cartData = $this->cartService->getCartData();
        
        return view('frontend.cart.index', [
            'cart' => $cartData['cart'],
            'cartItems' => $cartData['items']
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $this->cartService->addToCart($request->product_id, $request->quantity);
            $cartData = $this->cartService->getCartData();

            return response()->json([
                'success' => true,
                'message' => 'Product added to cart successfully!',
                'cart_total' => $cartData['total_amount'],
                'cart_items_count' => $cartData['total_items']
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $cartItem = $this->cartService->updateCartItem($id, $request->quantity);
            $cartData = $this->cartService->getCartData();

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully!',
                'item_total' => $cartItem->total,
                'cart_total' => $cartData['total_amount'],
                'cart_items_count' => $cartData['total_items']
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function remove($id)
    {
        try {
            $this->cartService->removeFromCart($id);
            $cartData = $this->cartService->getCartData();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart!',
                'cart_total' => $cartData['total_amount'],
                'cart_items_count' => $cartData['total_items']
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 403);
        }
    }

    public function clear()
    {
        try {
            $this->cartService->clearCart();

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getCartData()
    {
        $cartData = $this->cartService->getCartData();
        
        return response()->json([
            'cart_total' => $cartData['total_amount'],
            'cart_items_count' => $cartData['total_items'],
            'items' => $cartData['items']
        ]);
    }
}
