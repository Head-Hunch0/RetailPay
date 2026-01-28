<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales - RetailPay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.4.1/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-50 text-gray-800">
    @php
        $user = Auth::user();
    @endphp
    <div class="min-h-screen md:flex">
        @include('partials.sidebar')
        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 py-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-semibold">Sales</h1>
                        <p class="text-sm text-gray-500">Recent sales transactions</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-900 rounded-full">{{ now()->toFormattedDateString() }}</span>
                </div>

                @if (session('status'))
                    <div id="status-toast" class="fixed top-6 right-6 w-1/3 min-w-[260px] bg-green-600 text-white rounded-lg shadow-lg">
                        <div class="flex items-start justify-between gap-4 p-4">
                            <p class="text-sm font-medium">{{ session('status') }}</p>
                            <button type="button" class="text-white/80 hover:text-white" onclick="document.getElementById('status-toast').remove()">✕</button>
                        </div>
                    </div>
                @endif

                <div class="py-10">
                    <div class="bg-white border border-gray-500 rounded-xl shadow-xl p-8">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-4">
                            <div>
                                <h2 class="text-lg font-semibold">Sales Records</h2>
                                <p class="text-sm text-gray-500">Search by product or store</p>
                            </div>
                            <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                                <form method="GET" action="{{ route('sales.index') }}" class="w-full sm:w-80">
                                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, SKU, or store"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5">
                                </form>

                                @if ($user->role == 'storemanager')
                                        <button data-modal-target="request-transfer" data-modal-toggle="request-transfer" class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5">
                                            Record Sale 
                                        </button>
                                @endif
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2">Product</th>
                                        <th class="px-4 py-2">SKU</th>
                                        <th class="px-4 py-2">Store</th>
                                        <th class="px-4 py-2">Qty</th>
                                        <th class="px-4 py-2">Total</th>
                                        <th class="px-4 py-2">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($sales as $sale)
                                        <tr class="bg-white border-b">
                                            <td class="px-4 py-2 font-medium text-gray-900">{{ $sale->product?->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-2">{{ $sale->product?->SKU ?? 'N/A' }}</td>
                                            <td class="px-4 py-2">{{ $sale->store?->name ?? 'N/A' }}</td>
                                            <td class="px-4 py-2">{{ $sale->quantitySold }}</td>
                                            <td class="px-4 py-2">KSh {{ number_format($sale->totalPrice, 2) }}</td>
                                            <td class="px-4 py-2">{{ $sale->created_at?->format('M d, Y') }}</td>
                                        </tr>
                                    @empty
                                        <tr><td class="px-4 py-3" colspan="6">No sales found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- @if (($user->role == 'storemanager' || $user->role == 'branchmanager')) --}}
        <div id="request-transfer" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-lg max-h-full">
                <div class="relative bg-white rounded-lg shadow">
                    <div class="flex items-center justify-between p-4 border-b rounded-t">
                        <h3 class="text-lg font-semibold text-gray-900">Record Sale</h3>
                        <button type="button" class="text-gray-400 hover:text-gray-900" data-modal-hide="request-transfer">✕</button>
                    </div>
                    <form method="POST" action="{{ route('sales.store') }}" class="p-4 space-y-4">
                        @csrf

                        @error('storeID')
                            <p class="my-2 text-sm flex text-red-600">{{ $message }}</p>
                        @enderror
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Product</label>
                            <select name="productID" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5">
                                @foreach ($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->SKU }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900">Quantity</label>
                            <input name="quantity" type="number" min="1" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5">
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" data-modal-hide="request-transfer" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg">Cancel</button>
                            <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg">Submit</button>
                        </div>
                        <input type="text" name="storeID" value="{{ $user->managedStores()->first()->id ?? '' }}" hidden>
                    </form>
                </div>
            </div>
        </div>
    {{-- @endif --}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.4.1/flowbite.min.js"></script>
</body>
</html>
