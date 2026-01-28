<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        // retrieve search query
        $search = $request->string('search')->toString();

        /** @var User|null $user */
        $user = Auth::user();

        $storeIds = collect();
        if ($user) {
            if ($user->role === 'branchmanager') {
                // get store ids managed by the branch manager
                $branch = $user->managedBranch()->first();
                $storeIds = $branch ? $branch->stores()->pluck('id') : collect();
            } elseif ($user->role === 'storemanager') {
                // get store ids managed by the store manager
                $storeIds = $user->managedStores()->pluck('id');
            }
        }

        $sales = Sale::with(['product:id,name,SKU', 'store:id,name'])
        // filter by store ids for non-admin users
            ->when($user && $user->role !== 'admin', function ($query) use ($storeIds) {
                $query->whereIn('storeID', $storeIds->isNotEmpty() ? $storeIds : [0]);
            })
            // apply search filter
            ->when($search, function ($query, $search) {
                // filter sales by product name, SKU, or store name matching the search query
                $query->whereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('SKU', 'like', "%{$search}%");
                })->orWhereHas('store', function ($storeQuery) use ($search) {
                    $storeQuery->where('name', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->get();

        $products = Product::orderBy('name')->get(['id', 'name', 'SKU']);
        if ($user->role === 'storemanager') {
            $fromStores = $user->managedStores()->get(['id', 'name', 'branchID']);
            $toStores = $fromStores->isNotEmpty()
                ? Store::whereNotIn('id', $fromStores->pluck('id'))
                    ->whereIn('branchID', $fromStores->pluck('branchID'))
                    ->orderBy('name')
                    ->get(['id', 'name', 'branchID'])
                : collect();
        } 

        return view('sales.index', compact('sales', 'search', 'products'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'productID' => 'required|exists:products,id',
            'storeID' => 'required|exists:stores,id',
            'quantity' => 'required|integer|min:1',
        ]);

        /** @var User|null $user */

        // Authorization check
        if ($user->role !== 'admin') {
            $storeIds = match ($user->role) {
                // 'branchmanager' => $user->managedBranch()->first()?->stores()->pluck('id') ?? collect(),
                'storemanager' => $user->managedStores()->pluck('id'),
                // default => collect()
            };

            if (!$storeIds->contains($validated['storeID'])) {
                return back()->withErrors(['storeID' => 'Unauthorized store.'])->withInput();
            }
        }

        Sale::create([
            'productID' => $validated['productID'],
            'storeID' => $validated['storeID'],
            'quantitySold' => $validated['quantity'],
            'totalPrice' => $validated['quantity'] * Product::where('id', $validated['productID'])->value('price'),
        ]);

        Stock::where('productID', $validated['productID'])
            ->where('storeID', $validated['storeID'])
            ->decrement('quantity', $validated['quantity']);

        return redirect()->route('sales.index')->with('status', 'Sale recorded!');
    }
}
