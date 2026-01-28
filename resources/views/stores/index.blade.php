<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stores - RetailPay</title>
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
                        <h1 class="text-2xl font-semibold">Stores Overview</h1>
                        <p class="text-sm text-gray-500">Performance snapshot for each store</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-900 rounded-full">{{ now()->toFormattedDateString() }}</span>
                </div>

                <div class="grid gap-6">
                    @forelse ($stores as $store)
                        <div class="bg-white border border-gray-500 rounded-xl shadow-xl p-8">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900">{{ $store->name }}</h2>
                                    <p class="text-sm text-gray-500">{{ $store->location }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Branch: {{ $store->branch?->name ?? 'N/A' }}</p>
                                    <p class="text-xs text-gray-500">Manager: {{ $store->manager?->name ?? 'N/A' }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 text-base font-medium rounded-full">
                                    Sales: {{ $store->sales_count }}
                                </span>
                            </div>

                            <div class="grid gap-8 my-4 sm:grid-cols-2">
                                <div class="p-4 rounded-xl shadow-xl border border-gray-400">
                                    <p class="text">Revenue</p>
                                    <p class="text-xl font-semibold">KSh {{ number_format($store->revenue ?? 0, 2) }}</p>
                                </div>
                                <div class="p-4 rounded-xl shadow-xl border border-gray-400">
                                    <p class="text">Products</p>
                                    <p class="text-xl font-semibold">{{ $store->products }}</p>
                                </div>
                                <div class="p-4 rounded-xl shadow-xl border border-gray-400">
                                    <p class="text">Stock Items</p>
                                    <p class="text-xl font-semibold">{{ $store->stock_items }}</p>
                                </div>
                                <div class="p-4 rounded-xl shadow-xl border border-gray-400">
                                    <p class="text">Low Stock</p>
                                    <p class="text-xl font-semibold">{{ $store->low_stock }}</p>
                                </div>
                            </div>

                            <div class="mt-5 text-sm text-gray-600">
                                Transfers: <span class="font-medium text-gray-900">{{ $store->transfers }}</span>
                            </div>

                            <div class="mt-3 text-sm text-gray-600">
                                Low stock products:
                                @if ($store->low_stock_products->isNotEmpty())
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        @foreach ($store->low_stock_products as $productName)
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-rose-100 text-rose-700">
                                                {{ $productName }}
                                            </span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="font-medium text-gray-900">None</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="bg-white border border-gray-200 rounded-lg shadow p-6">
                            <p class="text-sm text-gray-600">No stores found.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.4.1/flowbite.min.js"></script>
</body>
</html>
