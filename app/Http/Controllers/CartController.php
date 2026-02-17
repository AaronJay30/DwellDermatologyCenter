<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

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
                'branch',
            ])
            ->get();

        $consultationItems = Schema::hasColumn('carts', 'item_type')
            ? $cartItems->filter(fn ($item) => $item->isConsultation())
            : $cartItems->filter(fn ($item) => $item->service_id === null);
        $serviceItems = $cartItems->filter(fn ($item) => $item->service_id !== null);

        // Group service items by branch
        $itemsByBranch = $serviceItems->groupBy(function ($item) {
            return $item->service && $item->service->category && $item->service->category->branch
                ? $item->service->category->branch->id
                : 'no-branch';
        });

        $total = $serviceItems->sum(function ($item) {
            return $item->service ? ($item->service->pricing['display_price'] * $item->quantity) : 0;
        });

        return view('cart.index', compact('cartItems', 'itemsByBranch', 'total', 'consultationItems'));
    }

    public function add(Request $request)
    {
        if ($request->get('item_type') === Cart::TYPE_CONSULTATION) {
            if (!Schema::hasColumn('carts', 'item_type')) {
                try {
                    Artisan::call('migrate', ['--force' => true]);
                } catch (\Throwable $e) {
                    if ($request->wantsJson() || $request->ajax()) {
                        return response()->json(['success' => false, 'message' => 'Database update required. Please run: php artisan migrate'], 500);
                    }
                    return back()->with('error', 'Database update required. Please run in terminal: php artisan migrate');
                }
            }
            $request->validate([
                'branch_id' => 'nullable|exists:branches,id',
            ]);
            $existing = Cart::where('user_id', Auth::id())
                ->where('item_type', Cart::TYPE_CONSULTATION)
                ->first();
            if ($existing) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json(['success' => true, 'count' => Cart::where('user_id', Auth::id())->sum('quantity')]);
                }
                return back()->with('info', 'Consultation is already in your cart.');
            }
            Cart::create([
                'user_id' => Auth::id(),
                'service_id' => null,
                'branch_id' => $request->branch_id,
                'item_type' => Cart::TYPE_CONSULTATION,
                'quantity' => 1,
            ]);
            $count = Cart::where('user_id', Auth::id())->sum('quantity');
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'count' => $count]);
            }
            return back()->with('success', 'Consultation added to cart. You can add services and checkout together.');
        }

        $request->validate([
            'service_id' => 'required|exists:services,id',
            'quantity' => 'integer|min:1|max:10'
        ]);

        $service = Service::findOrFail($request->service_id);
        
        if (!$service->is_active) {
            return back()->with('error', 'This service is currently unavailable.');
        }

        $cartQuery = Cart::where('user_id', Auth::id())->where('service_id', $request->service_id);
        if (Schema::hasColumn('carts', 'item_type')) {
            $cartQuery->where('item_type', Cart::TYPE_SERVICE);
        }
        $cartItem = $cartQuery->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity ?? 1;
            $cartItem->save();
        } else {
            $data = [
                'user_id' => Auth::id(),
                'service_id' => $request->service_id,
                'quantity' => $request->quantity ?? 1,
            ];
            if (Schema::hasColumn('carts', 'item_type')) {
                $data['item_type'] = Cart::TYPE_SERVICE;
            }
            if (Schema::hasColumn('carts', 'branch_id')) {
                $data['branch_id'] = $service->category->branch_id ?? null;
            }
            Cart::create($data);
        }

        $count = Cart::where('user_id', Auth::id())->sum('quantity');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'count' => $count,
            ]);
        }

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

        // Update quantity (only meaningful for service items; consultations are always quantity = 1)
        $cart->update(['quantity' => $request->quantity]);

        // Calculate updated totals for AJAX requests
        $cart->load('service.category.branch');

        // Guard against items that don't have an associated service (e.g. consultation line items)
        $itemTotal = $cart->service
            ? ($cart->service->pricing['display_price'] * $cart->quantity)
            : 0;

        // Sum totals only for items that actually have a service with pricing
        $cartTotal = Cart::where('user_id', Auth::id())
            ->with('service')
            ->get()
            ->sum(function ($item) {
                if (!$item->service) {
                    return 0;
                }
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

        // Remove the entire line item regardless of its current quantity.
        // Use the specific cart row (id) so we correctly handle consultation items as well.
        Cart::where('user_id', Auth::id())
            ->where('id', $cart->id)
            ->delete();

        // Recalculate totals, skipping items without a service (e.g. consultation)
        $cartTotal = Cart::where('user_id', Auth::id())
            ->with('service')
            ->get()
            ->sum(function ($item) {
                if (!$item->service) {
                    return 0;
                }
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