<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();
        /** @var User|null $user */
        $user = Auth::user();

        $storeIds = collect();

        if ($user) {
            // determine store IDs based on user role
            if ($user->role == 'branchmanager') {
                // get store ids managed by the branch manager
                $branch = $user->managedBranch()->first();
                $storeIds = $branch ? $branch->stores()->pluck('id') : collect();
            } elseif ($user->role == 'storemanager') {
                // get store ids managed by the store manager
                $storeIds = $user->managedStores()->pluck('id');
            }
        }

        // retrieve stock items with filters
        $stockItems = Stock::with(['product:id,name,SKU', 'store:id,name'])
            ->when($user && $user->role !== 'admin', function ($query) use ($storeIds) {
                $query->whereIn('storeID', $storeIds->isNotEmpty() ? $storeIds : [0]);
            })
            ->when($search, function ($query, $search) {
                $query->whereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('SKU', 'like', "%{$search}%");
                })->orWhereHas('store', function ($storeQuery) use ($search) {
                    $storeQuery->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('storeID')
            ->orderBy('productID')
            ->get();

        return view('stock.index', compact('stockItems', 'search'));
    }
}
