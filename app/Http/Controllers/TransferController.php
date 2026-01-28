<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Store;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TransferController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        /** @var User|null $user */
        $user = Auth::user();

        $storeIds = collect();
        $branchId = null;
        if ($user->role === 'branchmanager') {
            $branch = $user->managedBranch()->first();
            $branchId = $branch?->id;
            $storeIds = $branch ? $branch->stores()->pluck('id') : collect();
        } elseif ($user->role === 'storemanager') {
            $storeIds = $user->managedStores()->pluck('id');
        }

        $transfers = Transfer::with([
                'product:id,name,SKU',
                'fromStore:id,name,branchID',
                'toStore:id,name,branchID',
                'requester:id,name',
                'approver:id,name'
            ])
            ->when($user->role === 'branchmanager', function ($query) use ($branchId) {
                $query->whereHas('fromStore', fn ($q) => $q->where('branchID', $branchId))
                    ->orWhereHas('toStore', fn ($q) => $q->where('branchID', $branchId));
            })
            ->when($user->role === 'storemanager', function ($query) use ($storeIds) {
                $ids = $storeIds->isNotEmpty() ? $storeIds : [0];
                $query->where(function ($q) use ($ids) {
                    $q->whereIn('fromStoreID', $ids)
                        ->orWhereIn('toStoreID', $ids);
                });
            })
            ->when($search, function ($query, $search) {
                $query->whereHas('product', function ($productQuery) use ($search) {
                    $productQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('SKU', 'like', "%{$search}%");
                })->orWhereHas('fromStore', function ($storeQuery) use ($search) {
                    $storeQuery->where('name', 'like', "%{$search}%");
                })->orWhereHas('toStore', function ($storeQuery) use ($search) {
                    $storeQuery->where('name', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->get();

        if ($user->role === 'storemanager') {
            $transfers = $transfers->filter(fn ($transfer) =>
                $transfer->fromStore?->branchID &&
                $transfer->toStore?->branchID &&
                $transfer->fromStore->branchID === $transfer->toStore->branchID
            )->values();
        }

        $products = Product::orderBy('name')->get(['id', 'name', 'SKU']);
        if ($user->role === 'branchmanager') {
            $branch = $user->managedBranch()->first();
            $fromStores = $branch ? $branch->stores()->get(['id', 'name', 'branchID']) : collect();
            $toStores = Store::whereNotIn('id', $fromStores->pluck('id'))->orderBy('name')->get(['id', 'name', 'branchID']);
        } elseif ($user->role === 'storemanager') {
            $fromStores = $user->managedStores()->get(['id', 'name', 'branchID']);
            $toStores = $fromStores->isNotEmpty()
                ? Store::whereNotIn('id', $fromStores->pluck('id'))
                    ->whereIn('branchID', $fromStores->pluck('branchID'))
                    ->orderBy('name')
                    ->get(['id', 'name', 'branchID'])
                : collect();
        } else {
            $fromStores = collect();
            $toStores = collect();
        }

        return view('transfers.index', compact('transfers', 'search', 'products', 'fromStores', 'toStores'));
    }

    public function store(Request $request)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['storemanager', 'branchmanager'], true)) {
            return redirect()->route('transfers.index');
        }

        $data = $request->validate([
            'productID' => ['required', 'exists:products,id'],
            'fromStoreID' => ['required', 'exists:stores,id'],
            'toStoreID' => ['required', 'exists:stores,id', 'different:fromStoreID'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        if ($user->role === 'storemanager') {
            $allowedStoreIds = $user->managedStores()->pluck('id');
            if (!$allowedStoreIds->contains((int) $data['fromStoreID'])) {
                return redirect()->route('transfers.index')->with('status', 'You cannot request from that store.');
            }

            $fromStore = Store::find($data['fromStoreID']);
            $toStore = Store::find($data['toStoreID']);
            if (!$fromStore || !$toStore || $fromStore->branchID !== $toStore->branchID) {
                return redirect()->route('transfers.index')->with('status', 'Store managers can only transfer within the same branch.');
            }
        }

        if ($user->role === 'branchmanager') {
            $branch = $user->managedBranch()->first();
            $allowedStoreIds = $branch ? $branch->stores()->pluck('id') : collect();
            if (!$allowedStoreIds->contains((int) $data['fromStoreID'])) {
                return redirect()->route('transfers.index')->with('status', 'You cannot request from that store.');
            }
        }

        $fromStock = Stock::where('productID', $data['productID'])
            ->where('storeID', $data['fromStoreID'])
            ->first();

        if (!$fromStock || $fromStock->quantity < $data['quantity']) {
            return redirect()->route('transfers.index')->with('status', 'Not enough stock for this transfer.');
        }

        Transfer::create([
            'productID' => $data['productID'],
            'fromStoreID' => $data['fromStoreID'],
            'toStoreID' => $data['toStoreID'],
            'quantity' => $data['quantity'],
            'requestedBy' => $user->id,
            'approvedBy' => null,
            'status' => 'pending',
        ]);

        return redirect()->route('transfers.index')->with('status', 'Transfer request submitted.');
    }

    public function update(Request $request, Transfer $transfer)
    {
        /** @var User|null $user */
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($transfer->requestedBy === $user->id) {
            return redirect()->route('transfers.index')->with('status', 'You cannot approve your own transfer.');
        }

        $requester = $transfer->requester()->first();
        if ($requester && $requester->role === 'branchmanager' && $user->role !== 'branchmanager') {
            return redirect()->route('transfers.index')->with('status', 'Only another branch manager can approve this transfer.');
        }

        $data = $request->validate([
            'status' => ['required', 'in:approved,rejected'],
        ]);

        if ($transfer->status !== 'pending') {
            return redirect()->route('transfers.index')->with('status', 'Transfer already processed.');
        }

        $fromBranchId = $transfer->fromStore?->branchID;
        $toBranchId = $transfer->toStore?->branchID;
        $isInterStore = $fromBranchId && $toBranchId && $fromBranchId === $toBranchId;

        $canApprove = false;
        if ($isInterStore && $user->role === 'storemanager') {
            $managedStoreIds = $user->managedStores()->pluck('id');
            $canApprove = $managedStoreIds->contains($transfer->fromStoreID)
                || $managedStoreIds->contains($transfer->toStoreID);
        }

        if (!$isInterStore && $user->role === 'branchmanager') {
            $branch = $user->managedBranch()->first();
            $canApprove = $branch && ($branch->id === $fromBranchId || $branch->id === $toBranchId);
        }

        if (!$canApprove) {
            return redirect()->route('transfers.index')->with('status', 'You cannot approve this transfer.');
        }

        $transfer->approvedBy = $user->id;
        $transfer->status = $data['status'];

        if ($data['status'] === 'approved') {
            $fromStock = Stock::where('productID', $transfer->productID)
                ->where('storeID', $transfer->fromStoreID)
                ->first();

            if (!$fromStock || $fromStock->quantity < $transfer->quantity) {
                return redirect()->route('transfers.index')->with('status', 'Insufficient stock to approve.');
            }

            $fromStock->decrement('quantity', $transfer->quantity);

            $toStock = Stock::where('productID', $transfer->productID)
                ->where('storeID', $transfer->toStoreID)
                ->first();

            if ($toStock) {
                $toStock->increment('quantity', $transfer->quantity);
            } else {
                Stock::create([
                    'productID' => $transfer->productID,
                    'storeID' => $transfer->toStoreID,
                    'quantity' => $transfer->quantity,
                    'minimum' => 10,
                ]);
            }
        }

        $transfer->save();

        return redirect()->route('transfers.index')->with('status', 'Transfer updated.');
    }
}
