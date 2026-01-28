<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MainController extends Controller
{

    // statistics for the admin dashboard
    public function index()
    {
        // get logged in user
        $user = Auth::user();
        /** @var User|null $user */
    
        // empty collection for store ids
        $storeIds = collect();
        $branch = null;


        if ($user && $user->role === 'branchmanager') {
            $branch = $user->managedBranch()->first();
            $storeIds = $branch ? $branch->stores()->pluck('id') : collect();
        } elseif ($user && $user->role === 'storemanager') {
            $storeIds = $user->managedStores()->pluck('id');
        }

        // base queries query builders 
        $salesQuery = Sale::query();
        $stockQuery = Stock::query();
        $transferQuery = Transfer::query();

        if ($user && $user->role !== 'admin') {
            $ids = $storeIds->isNotEmpty() ? $storeIds : [0];
            // filter queries by store ids
            $salesQuery->whereIn('storeID', $ids);
            $stockQuery->whereIn('storeID', $ids);
            $transferQuery->whereIn('fromStoreID', $ids)->orWhereIn('toStoreID', $ids);
        }

        // calculate totals
        // we clone to avoid modifying original querries
        $totalSales = (clone $salesQuery)->count();
        $totalRevenue = (clone $salesQuery)->sum('totalPrice');

        $stats = [
            // if role is admin we count all users minus the admin others see null but the view disables this stat line for non admins
            'users' => $user && $user->role === 'admin' ? User::where('role', '!=', 'admin')->count() : null,
            // admin sees all branches count branch managers see their branch count store managers see null also disabled in the view for store managers
            'branches' => $user && $user->role === 'admin' ? Branch::count() : ($branch ? 1 : null),
            'branch_name' => $branch?->name,
            // store count
            'stores' => $user && $user->role === 'admin' ? Store::count() : $storeIds->count(),
            // product count
            'products' => $user && $user->role === 'admin'
                ? Product::count()
                : Stock::whereIn('storeID', $storeIds)->distinct('productID')->count('productID'),
            // stock items and quantity
            'stock_items' => (clone $stockQuery)->count(),
            'stock_qty' => (clone $stockQuery)->sum('quantity') ?? 0,

            'sales' => $totalSales,
            'transfers' => (clone $transferQuery)->count(),
            'pending_transfers' => (clone $transferQuery)->where('status', 'pending')->count(),
            'revenue' => $totalRevenue,
            // 'low_stock' => (clone $stockQuery)->whereColumn('quantity', '<=', 'minimum')->count() ?? 0,
            'low_stock' => $storeIds->isNotEmpty()
                ? (clone $stockQuery)->whereColumn('quantity', '<=', 'minimum')->count()
                : 0,
        ];
        
        // recent sales 
        $recentSales = Sale::with(['product:id,name', 'store:id,name'])
            ->when($user && $user->role !== 'admin', fn ($query) => $query->whereIn('storeID', $storeIds))
            ->latest()
            ->take(10)
            ->get();

        // recent transfers
        $recentTransfers = Transfer::with(['product:id,name', 'fromStore:id,name', 'toStore:id,name'])
            ->when($user && $user->role !== 'admin', function ($query) use ($storeIds) {
                $ids = $storeIds->isNotEmpty() ? $storeIds : [0];
                $query->whereIn('fromStoreID', $ids)->orWhereIn('toStoreID', $ids);
            })
            ->latest()
            ->take(10)
            ->get();

        // sales by store
        $salesByStore = Store::withSum('sales as revenue', 'totalPrice')
            ->when($user && $user->role !== 'admin', fn ($query) => $query->whereIn('id', $storeIds))
            ->orderBy('name')
            ->get(['id', 'name']);

        // sales by day for last 7 days
        $salesByDay = Sale::when($user && $user->role !== 'admin', fn ($query) => $query->whereIn('storeID', $storeIds))
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->orderBy('created_at')
            ->get(['created_at', 'totalPrice'])
            ->groupBy(fn ($sale) => $sale->created_at->toDateString())
            ->map(fn ($sales) =>  $sales->sum('totalPrice'));

        $dailyLabels = [];
        $dailyTotals = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i)->toDateString();
            $dailyLabels[] = Carbon::parse($day)->format('D');
            $dailyTotals[] = (float) ($salesByDay[$day] ?? 0);
        }

        $topProducts = Product::withSum([
                'sales as qty' => fn ($query) => $query
                    ->when($user && $user->role !== 'admin', fn ($q) => $q->whereIn('storeID', $storeIds))
            ], 'quantitySold')
            ->orderByDesc('qty')
            ->take(10)
            ->get(['id', 'name']);

        $lowStockItems = Stock::with(['product:id,name', 'store:id,name'])
            ->when($user && $user->role !== 'admin', fn ($query) => $query->whereIn('storeID', $storeIds))
            ->whereColumn('quantity', '<=', 'minimum')
            ->orderBy('quantity')
            ->take(10)
            ->get();

        return view('dashboard', compact('stats','recentSales','recentTransfers','salesByStore','dailyLabels','dailyTotals','topProducts','lowStockItems'));
    }

    
    public function stores()
    {
        // get logged in user
        /** @var User|null $user */
        $user = Auth::user();
        $storeIds = collect();

        // get store ids for non admin users
        if ($user && $user->role === 'branchmanager') {
            // get user's managed branch
            $branch = $user->managedBranch()->first();
            // get store ids for the branch
            $storeIds = $branch ? $branch->stores()->pluck('id') : collect();
        } elseif ($user && $user->role === 'storemanager') {
            // get store ids for the store manager
            $storeIds = $user->managedStores()->pluck('id');
        }

        // eager load stores with related data
        $stores = Store::with([
            // include branch info
            'branch:id,name', 
            // include manager info
            'manager:id,name',
            // include sales info
            'sales:id,storeID,totalPrice', 
            // include stock items with product info
            'stockItems.product:id,name', 
            // include incoming transfers 
            'incomingTransfers:id,toStoreID',
            // include outgoing transfers
            'outgoingTransfers:id,fromStoreID'
        ])
            ->when($user && $user->role !== 'admin', fn($query) => $query->whereIn('id', $storeIds->isNotEmpty() ? $storeIds : [0]))
            ->orderBy('name')
            ->get();

        // calculate additional statistics for each store
        foreach ($stores as $store) {
            // total revenue and sales count
            $store->revenue = $store->sales->sum('totalPrice');
            $store->sales_count = $store->sales->count();

            // stock statistics
            $store->stock_items = $store->stockItems->count();
            $store->low_stock = $store->stockItems
                ->filter(fn($item) => $item->quantity <= $item->minimum)
                ->count();
            // get Unique product count in stock
            $store->products = $store->stockItems
            // unique product ids
                ->pluck('product.id')
            // filter out nulls
                ->filter()
            // remove duplicates
                ->unique()
            // count unique ids
                ->count();
            // low stock product names
            $store->low_stock_products = $store->stockItems
            // filter low stock items
                ->filter(fn($item) => $item->quantity <= $item->minimum)
            // get product names
                ->pluck('product.name')
            // remove nulls
                ->filter()
            // get unique names
                ->unique()
            // reindex array
                ->values();

            // total transfers (incoming + outgoing)
            $store->transfers = $store->incomingTransfers->count() + $store->outgoingTransfers->count();
        }

        return view('stores.index', compact('stores'));
    }

    public function branches()
    {
        // eager load branches with related data
        $branches = Branch::with([
            'manager:id,name',
            'stores:id,branchID,name',
            'stores.sales:id,storeID,totalPrice'
        ])
        // include store count for each branch
            ->withCount('stores')
            ->orderBy('name')
            ->get();

        foreach ($branches as $branch) {
            // get store ids for the branch
            $storeIds = $branch->stores->pluck('id');
            // calculate revenue and sales count for the branch (all sales from its stores)
            $sales = $branch->stores->flatMap->sales;

            // calculate sum of all sales in the branch
            $branch->revenue = $sales->sum('totalPrice');
            // count total sales in the branch
            $branch->sales_count = $sales->count();

            // stock statistics for the branch (all stock from its stores)
            $branch->stock_items = Stock::whereIn('storeID', $storeIds)->count();
            // low stock items for the branch (quantity <= minimum)
            $lowStockItems = Stock::with('product:id,name')
                ->whereIn('storeID', $storeIds)
                ->whereColumn('quantity', '<=', 'minimum')
                ->get();
            // count low stock items
            $branch->low_stock = $lowStockItems->count();
            // get low stock product names
            $branch->low_stock_products = $lowStockItems
                ->pluck('product.name')
                ->filter()
                ->unique()
                ->values();

            // count unique product ids in the branch's stock
            $branch->products = Stock::whereIn('storeID', $storeIds)
                ->distinct('productID')
                ->count('productID');
        }

        return view('branches.index', compact('branches'));
    }
}
