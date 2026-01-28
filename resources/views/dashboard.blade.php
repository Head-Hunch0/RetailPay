<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RetailPay Dashboard</title>
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
                <h1 class="text-2xl font-semibold">Dashboard</h1>
                <p class="text-sm text-gray-500">Overview of branches, stores, products, stock, sales, and transfers</p>
            </div>
            <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-blue-700 rounded-full">{{ now()->toFormattedDateString() }}</span>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @if ($user->role === 'admin')
                <div class="p-4 rounded-lg border border-gray-400 shadow-xl rounded-xl">
                    <p class="text text-gray-700">Users</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['users'] }}</p>
                </div>
                <div class="p-4 rounded-lg border border-gray-400 shadow-xl rounded-xl">
                    <p class="text text-gray-700">Branches</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['branches'] }}</p>
                </div>
            @elseif ($user->role === 'branchmanager')
                <div class="p-4 rounded-lg border border-gray-400 shadow-xl rounded-xl">
                    <p class="text text-gray-700">Branch</p>
                    <p class="text-2xl font-semibold text-gray-900">{{ $stats['branch_name'] ?? 'N/A' }}</p>
                </div>
            @endif

            <div class="p-4 rounded-lg border border-gray-400 shadow-xl rounded-xl">
                <p class="text text-gray-700">Stores</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['stores'] }}</p>
            </div>
            <div class="p-4 rounded-lg border border-gray-400 shadow-xl rounded-xl">
                <p class="text text-gray-700">Products</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['products'] }}</p>
            </div>
            <div class="p-4 rounded-lg border border-gray-400 shadow-xl rounded-xl">
                <p class="text text-gray-700">Stock Items</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['stock_items'] }}</p>
                <p class="text-xs text-gray-700">Total units: {{ $stats['stock_qty'] }}</p>
            </div>
            <div class="p-4 rounded-lg border border-gray-400 shadow-xl rounded-xl">
                <p class="text text-gray-700">Low Stock</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['low_stock'] }}</p>
                @if ($stats['low_stock'] > 0)
                    <p class="text-xs text-gray-700">Needs attention</p>
                @endif
            </div>
            <div class="p-4 rounded-lg border border-gray-400 shadow-xl rounded-xl">
                <p class="text text-gray-700">Sales</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['sales'] }}</p>
            </div>
            <div class="p-4 rounded-lg border border-gray-400 shadow-xl rounded-xl">
                <p class="text text-gray-700">Revenue</p>
                <p class="text-2xl font-semibold text-gray-900">KSh {{ number_format($stats['revenue'], 2) }}</p>
                <p class="text-xs text-gray-700">Transfers: {{ $stats['transfers'] }}</p>
            </div>
            @if ($user && $user->role !== 'admin')
                
            <div class="p-4 rounded-lg border border-gray-400 shadow-xl rounded-xl">
                <p class="text text-gray-700">Pending Transfers</p>
                <p class="text-2xl font-semibold text-gray-900">{{ $stats['pending_transfers'] }}</p>
            </div>
            @endif
        </div>

        <div class="grid gap-6 mt-8 lg:grid-cols-2">
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">Sales (Last 7 Days)</h2>
                    <span class="text-xs text-gray-500">KSh</span>
                </div>
                <div id="salesChart" class="w-full"></div>
            </div>

            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold">Revenue by Store</h2>
                    <span class="text-xs text-gray-500">KSh</span>
                </div>
                <div id="storeRevenueChart" class="w-full"></div>
            </div>
        </div>

        <div class="grid gap-6 mt-8 lg:grid-cols-2">
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow">
                <h2 class="text-lg font-semibold mb-4">Top Products (Units Sold)</h2>
                <ul class="space-y-3">
                    @forelse ($topProducts as $product)
                        <li class="flex items-center justify-between">
                            <span class="text-sm text-gray-700">{{ $product->name }}</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $product->qty }}</span>
                        </li>
                    @empty
                        <li class="text-sm text-gray-500">No sales data yet.</li>
                    @endforelse
                </ul>
            </div>

            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow">
                <h2 class="text-lg font-semibold mb-4">Low Stock Alerts</h2>
                <ul class="space-y-3">
                    @forelse ($lowStockItems as $item)
                        <li class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $item->product?->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $item->store?->name ?? 'N/A' }}</p>
                            </div>
                            <span class="text-sm font-semibold text-rose-600">{{ $item->quantity }} / {{ $item->minimum }}</span>
                        </li>
                    @empty
                        <li class="text-sm text-gray-500">No low stock items.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <div class="grid gap-6 mt-8 lg:grid-cols-2">
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow">
                <h2 class="text-lg font-semibold mb-4">Recent Sales</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-4 py-2">Product</th>
                                <th class="px-4 py-2">Store</th>
                                <th class="px-4 py-2">Qty</th>
                                <th class="px-4 py-2">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentSales as $sale)
                                <tr class="bg-white border-b">
                                    <td class="px-4 py-2 font-medium text-gray-900">{{ $sale->product?->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $sale->store?->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $sale->quantitySold }}</td>
                                    <td class="px-4 py-2">KSh {{ number_format($sale->totalPrice, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td class="px-4 py-3" colspan="4">No sales yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow">
                <h2 class="text-lg font-semibold mb-4">Recent Transfers</h2>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="px-4 py-2">Product</th>
                                <th class="px-4 py-2">From</th>
                                <th class="px-4 py-2">To</th>
                                <th class="px-4 py-2">Qty</th>
                                <th class="px-4 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentTransfers as $transfer)
                                <tr class="bg-white border-b">
                                    <td class="px-4 py-2 font-medium text-gray-900">{{ $transfer->product?->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $transfer->fromStore?->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $transfer->toStore?->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-2">{{ $transfer->quantity }}</td>
                                    <td class="px-4 py-2">
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                            @if ($transfer->status === 'approved') text-green-700 bg-green-100
                                            @elseif ($transfer->status === 'rejected') text-red-700 bg-red-100
                                            @else text-yellow-700 bg-yellow-100 @endif">
                                            {{ ucfirst($transfer->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td class="px-4 py-3" colspan="5">No transfers yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.4.1/flowbite.min.js"></script>
    <script>
        const salesChart = new ApexCharts(document.querySelector('#salesChart'), {
            chart: {
                type: 'area',
                height: 280,
                toolbar: { show: false }
            },
            series: [{
                name: 'Revenue',
                data: @json($dailyTotals)
            }],
            xaxis: {
                categories: @json($dailyLabels)
            },
            colors: ['#3b82f6'],
            stroke: { curve: 'smooth', width: 3 },
            dataLabels: { enabled: false },
            fill: {
                type: 'gradient',
                gradient: { opacityFrom: 0.35, opacityTo: 0.05 }
            },
            yaxis: {
                labels: {
                    formatter: (val) => `KSh ${val.toFixed(0)}`
                }
            }
        });
        salesChart.render();

        const storeRevenueChart = new ApexCharts(document.querySelector('#storeRevenueChart'), {
            chart: {
                type: 'bar',
                height: 280,
                toolbar: { show: false }
            },
            series: [{
                name: 'Revenue',
                data: @json($salesByStore->pluck('revenue'))
            }],
            xaxis: {
                categories: @json($salesByStore->pluck('name'))
            },
            colors: ['#10b981'],
            dataLabels: { enabled: false },
            plotOptions: {
                bar: { borderRadius: 6, columnWidth: '45%' }
            },
            yaxis: {
                labels: {
                    formatter: (val) => `KSh ${val.toFixed(0)}`
                }
            }
        });
        storeRevenueChart.render();
    </script>
</body>
</html>
