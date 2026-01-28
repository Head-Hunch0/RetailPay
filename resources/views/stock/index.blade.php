<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stock - RetailPay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.4.1/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="min-h-screen md:flex">
        @include('partials.sidebar')
        <main class="flex-1">
            <div class="max-w-7xl mx-auto px-4 py-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-semibold">Stock</h1>
                        <p class="text-sm text-gray-500">Current inventory levels</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-900 rounded-full">{{ now()->toFormattedDateString() }}</span>
                </div>

                <div class="py-10">
                    <div class="bg-white border border-gray-500 rounded-xl shadow-xl p-8">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-4">
                            <div>
                                <h2 class="text-lg font-semibold">Stock Items</h2>
                                <p class="text-sm text-gray-500">Search by product or store</p>
                            </div>
                            <form method="GET" action="{{ route('stock.index') }}" class="w-full sm:w-80">
                                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, SKU, or store"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5">
                            </form>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2">Product</th>
                                    <th class="px-4 py-2">SKU</th>
                                    <th class="px-4 py-2">Store</th>
                                    <th class="px-4 py-2">Quantity</th>
                                    <th class="px-4 py-2">Minimum</th>
                                    <th class="px-4 py-2">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($stockItems as $item)
                                    @php
                                        $isLow = $item->quantity <= $item->minimum;
                                    @endphp
                                    <tr class="bg-white border-b">
                                        <td class="px-4 py-2 font-medium text-gray-900">{{ $item->product?->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-2">{{ $item->product?->SKU ?? 'N/A' }}</td>
                                        <td class="px-4 py-2">{{ $item->store?->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-2">{{ $item->quantity }}</td>
                                        <td class="px-4 py-2">{{ $item->minimum }}</td>
                                        <td class="px-4 py-2">
                                            @if ($isLow)
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-rose-100 text-rose-700">Low</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-emerald-100 text-emerald-700">OK</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td class="px-4 py-3" colspan="6">No stock found.</td></tr>
                                @endforelse
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.4.1/flowbite.min.js"></script>
</body>
</html>
