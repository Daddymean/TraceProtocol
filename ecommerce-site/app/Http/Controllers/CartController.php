```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Product;
use App\Cart;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cart = Cart::where('user_id', auth()->id())->first();

        return view('cart.index', compact('cart'));
    }

    public function store(Request $request)
    {
        $cart = Cart::firstOrCreate(['user_id' => auth()->id()]);

        $product = Product::find($request->product_id);

        $cart->products()->attach($product);

        return redirect()->back()->with('message', 'Product added to cart successfully!');
    }

    public function destroy(Request $request)
    {
        $cart = Cart::where('user_id', auth()->id())->first();

        $product = Product::find($request->product_id);

        $cart->products()->detach($product);

        return redirect()->back()->with('message', 'Product removed from cart successfully!');
    }
}
```