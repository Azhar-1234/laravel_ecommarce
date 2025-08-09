<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Get products by categories
        $fruitsVeges = Product::active()
            ->inStock()
            ->where('category', 'like', '%fruit%')
            ->orWhere('category', 'like', '%vegetable%')
            ->limit(6)
            ->get();
        return view('frontend.master', compact('fruitsVeges'));
    }
}
