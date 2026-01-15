<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:patient');
    }

    public function index()
    {
        $cartItems = Cart::where('user_id', Auth::id())
            ->with([
                'service.category.branch',
                'service.images',
                'service.promoServices.promotion',
            ])
            ->get();
        
        // Group items by branch
        $itemsByBranch = $cartItems->groupBy(function ($item) {
            return $item->service->category->branch->id ?? 'no-branch';
        });
        
        $total = $cartItems->sum(function ($item) {
            return $item->service->pricing['display_price'] * $item->quantity;
        });

        return view('cart.index', compact('cartItems', 'itemsByBranch', 'total'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'quantity' => 'integer|min:1|max:10'
        ]);

        $service = Service::findOrFail($request->service_id);
        
        if (!$service->is_active) {
            return back()->with('error', 'This service is currently unavailable.');
        }

        $cartItem = Cart::where('user_id', Auth::id())
            ->where('service_id', $request->service_id)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity ?? 1;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'service_id' => $request->service_id,
                'quantity' => $request->quantity ?? 1,
            ]);
        }

        $count = Cart::where('user_id', Auth::id())->sum('quantity');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'count' => $count,
            ]);
        }

        // Silent fallback for non-AJAX requests
        return back();
    }

    public function update(Request $request, Cart $cart)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        $cart->update(['quantity' => $request->quantity]);

        // Calculate updated totals for AJAX requests
        $cart->load('service.category.branch');
        $itemTotal = $cart->service->pricing['display_price'] * $cart->quantity;
        $cartTotal = Cart::where('user_id', Auth::id())
            ->get()
            ->sum(function ($item) {
                return $item->service->pricing['display_price'] * $item->quantity;
            });

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'quantity' => $cart->quantity,
                'item_total' => $itemTotal,
                'cart_total' => $cartTotal,
            ]);
        }

        // Silent fallback for non-AJAX requests
        return back();
    }

    public function remove(Cart $cart)
    {
        if ($cart->user_id !== Auth::id()) {
            abort(403);
        }

        // Remove the entire line item regardless of its current quantity
        Cart::where('user_id', Auth::id())
            ->where('service_id', $cart->service_id)
            ->delete();

        $cartTotal = Cart::where('user_id', Auth::id())
            ->get()
            ->sum(function ($item) {
                return $item->service->pricing['display_price'] * $item->quantity;
            });
        $cartCount = Cart::where('user_id', Auth::id())->sum('quantity');

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'cart_total' => $cartTotal,
                'count' => $cartCount,
            ]);
        }

        return back();
    }

    public function clear()
    {
        Cart::where('user_id', Auth::id())->delete();

        return back()->with('success', 'Cart cleared successfully!');
    }

    public function count()
    {
        $count = Cart::where('user_id', Auth::id())->sum('quantity');
        
        return response()->json(['count' => $count]);
    }
}