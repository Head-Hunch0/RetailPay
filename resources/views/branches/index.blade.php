<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Branches - RetailPay</title>
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
                        <h1 class="text-2xl font-semibold">Branches Overview</h1>
                        <p class="text-sm text-gray-500">Performance snapshot for each branch</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-900 rounded-full">{{ now()->toFormattedDateString() }}</span>
                </div>

                <div class="grid gap-6 ">
                    @forelse ($branches as $branch)
                        <div class="bg-white border border-gray-500 rounded-xl shadow-xl p-8">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900">{{ $branch->name }}</h2>
                                    <p class="text-sm text-gray-500">{{ $branch->location }}</p>
                                    <p class="text-xs text-gray-500 mt-1">Manager: {{ $branch->manager?->name ?? 'N/A' }}</p>
                                </div>
                                <span class="inline-flex items-center px-2.5 py-1 text-base font-medium rounded-full">
                                    Stores: {{ $branch->stores_count }}
                                </span>
                            </div>

                            <div class="grid gap-8 my-4 sm:grid-cols-2">
                                <div class="p-4 rounded-xl shadow-xl border border-gray-400">
                                    <p class="text">Revenue</p>
                                    <p class="text-xl font-semibold ">KSh {{ number_format($branch->revenue ?? 0, 2) }}</p>
                                </div>
                                <div class="p-4 rounded-xl shadow-xl border border-gray-400">
                                    <p class="text">Sales</p>
                                    <p class="text-xl font-semibold ">{{ $branch->sales_count }}</p>
                                </div>
                                <div class="p-4 rounded-xl shadow-xl border border-gray-400">
                                    <p class="text">Products</p>
                                    <p class="text-xl font-semibold ">{{ $branch->products }}</p>
                                </div>
                                <div class="p-4 rounded-xl shadow-xl border border-gray-400">
                                    <p class="text">Low Stock</p>
                                    <p class="text-xl font-semibold ">{{ $branch->low_stock }}</p>
                                </div>
                            </div>

                            <div class="mt-5 text-sm text-gray-600">
                                Stock items: <span class="font-medium text-gray-900">{{ $branch->stock_items }}</span>
                            </div>
                            <div class="mt-3 text-sm text-gray-600">
                                Low stock products:
                                @if ($branch->low_stock_products->isNotEmpty())
                                    <div class="my-2 flex flex-wrap gap-2">
                                        @foreach ($branch->low_stock_products as $productName)
                                            <span class="inline-flex items-center px-2.5 py-1.5 text-xs font-medium rounded-full bg-rose-100 text-rose-700">
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
                            <p class="text-sm text-gray-600">No branches found.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.4.1/flowbite.min.js"></script>
</body>
</html>
