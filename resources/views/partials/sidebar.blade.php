@php
    $route = Route::current()->getName();
    $user = Auth::user();
@endphp
<aside class="w-full md:w-64 md:shrink-0 md:h-screen md:sticky md:top-0 bg-white border-r border-gray-200">
    <div class="px-4 py-6">
        <div class="mb-6">
            <h2 class="text-lg font-semibold text-gray-900">RetailPay</h2>
            <p class="text-xs text-gray-500">Inventory & Sales</p>
        </div>
        <ul class="space-y-2 pb-5">
            <a href="/dashboard" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg {{ $route === 'dashboard' ? 'text-gray-900 bg-gray-100' : 'text-gray-600 hover:bg-gray-100' }}">
                <span>Dashboard</span>
            </a>
            @if ($user && $user->role !== 'admin')
            <a href="{{ route('branches.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg {{ $route === 'branches.index' ? 'text-gray-900 bg-gray-100' : 'text-gray-600 hover:bg-gray-100' }}">Branches</a>
            @endif
            <a href="{{ route('stores.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg {{ $route === 'stores.index' ? 'text-gray-900 bg-gray-100' : 'text-gray-600 hover:bg-gray-100' }}">Stores</a>
            <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg {{ $route === 'products.index' ? 'text-gray-900 bg-gray-100' : 'text-gray-600 hover:bg-gray-100' }}">Products</a>
            <a href="{{ route('stock.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg {{ $route === 'stock.index' ? 'text-gray-900 bg-gray-100' : 'text-gray-600 hover:bg-gray-100' }}">Stock</a>
            <a href="{{ route('sales.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg {{ $route === 'sales.index' ? 'text-gray-900 bg-gray-100' : 'text-gray-600 hover:bg-gray-100' }}">Sales</a>
            <a href="{{ route('transfers.index') }}" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg {{ $route === 'transfers.index' ? 'text-gray-900 bg-gray-100' : 'text-gray-600 hover:bg-gray-100' }}">Transfers</a>
        </ul>
        <div class="pt-4 border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 text-sm font-medium text-red-600 rounded-lg hover:bg-red-50">
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>


