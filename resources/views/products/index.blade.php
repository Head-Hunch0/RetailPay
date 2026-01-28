<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - RetailPay</title>
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
                        <h1 class="text-2xl font-semibold">Products</h1>
                        <p class="text-sm text-gray-500">Manage your catalog</p>
                    </div>
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium text-gray-900 rounded-full">{{ now()->toFormattedDateString() }}</span>
                </div>

                @if (session('status'))
                    <div id="status-toast" class="fixed top-6 right-6 w-full max-w-sm sm:max-w-md md:max-w-lg lg:max-w-xl bg-green-600 text-white rounded-lg shadow-lg">
                        <div class="flex items-start justify-between gap-4 p-4">
                            <p class="text-sm font-medium">{{ session('status') }}</p>
                            <button type="button" class="text-white/80 hover:text-white" onclick="document.getElementById('status-toast').remove()">✕</button>
                        </div>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 p-3 text-sm text-red-700 bg-red-100 rounded-lg">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="py-10">

                
                <div class="bg-white border border-gray-500 rounded-xl shadow-xl p-8 ">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-4">
                        <div>
                            <h2 class="text-lg font-semibold">All Products</h2>
                            <p class="text-sm text-gray-500">Search, add, and manage products</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                            <form method="GET" action="{{ route('products.index') }}" class="w-full sm:w-80">
                                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name or SKU"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5">
                            </form>
                            <button data-modal-target="add-product" data-modal-toggle="add-product" class="text-white bg-blue-600 hover:bg-blue-700 font-medium rounded-lg text-sm px-5 py-2.5">
                                Add Product
                            </button>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2">Name</th>
                                        <th class="px-4 py-2">SKU</th>
                                        <th class="px-4 py-2">Price</th>
                                        <th class="px-4 py-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($products as $product)
                                        <tr class="bg-white border-b space-y-4">
                                            <td class="px-4 py-2 font-medium text-gray-900">{{ $product->name }}</td>
                                            <td class="px-4 py-2">{{ $product->SKU }}</td>
                                            <td class="px-4 py-2">KSh {{ number_format($product->price, 2) }}</td>
                                            <td class="px-4 py-2 flex items-center gap-4">
                                                <button data-modal-target="edit-product-{{ $product->id }}" data-modal-toggle="edit-product-{{ $product->id }}" class="text-blue-600 hover:text-blue-800">Edit</button>
                                                <form method="POST" action="{{ route('products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-800">Delete</button>
                                                </form>
                                            </td>
                                        </tr>

                                        <div id="edit-product-{{ $product->id }}" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                            <div class="relative p-4 w-full max-w-lg max-h-full">
                                                <div class="relative bg-white rounded-lg shadow">
                                                    <div class="flex items-center justify-between p-8 border-b rounded-t">
                                                        <h3 class="text-lg font-semibold text-gray-900">Edit Product</h3>
                                                        <button type="button" class="text-gray-400 hover:text-gray-900" data-modal-hide="edit-product-{{ $product->id }}">✕</button>
                                                    </div>
                                                    <form method="POST" action="{{ route('products.update', $product) }}" class="p-4 space-y-4">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="px-6 space-y-4">

                                                        <div>
                                                            <label class="block mb-2 text-sm font-medium text-gray-900">Name</label>
                                                            <input name="name" type="text" value="{{ $product->name }}" required
                                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5">
                                                        </div>
                                                        <div>
                                                            <label class="block mb-2 text-sm font-medium text-gray-900">SKU</label>
                                                            <input name="SKU" type="text" value="{{ $product->SKU }}" required
                                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5">
                                                        </div>
                                                        <div>
                                                            <label class="block mb-2 text-sm font-medium text-gray-900">Price (KSh)</label>
                                                            <input name="price" type="number" step="0.01" min="0" value="{{ $product->price }}" required
                                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5">
                                                        </div>
                                                        <div>
                                                            <label class="block mb-2 text-sm font-medium text-gray-900">Description</label>
                                                            <textarea name="description" rows="3" required
                                                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5">{{ $product->description }}</textarea>
                                                        </div>
                                                        <div class="flex justify-end gap-2 p-4">
                                                            <button type="button" data-modal-hide="edit-product-{{ $product->id }}" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg">Cancel</button>
                                                            <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg">Save</button>
                                                        </div>

                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <tr><td class="px-4 py-3" colspan="4">No products found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                    </div>
                </div>

                </div>
            </div>
        </main>
    </div>

    <div id="add-product" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative p-4 w-full max-w-lg max-h-full">
            <div class="relative bg-white rounded-lg shadow">
                <div class="flex items-center justify-between p-4 border-b rounded-t">
                    <h3 class="text-lg font-semibold text-gray-900">Add Product</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-900" data-modal-hide="add-product">✕</button>
                </div>
                <form method="POST" action="{{ route('products.store') }}" class="p-4 space-y-4">
                    @csrf
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900" for="name">Name</label>
                        <input id="name" name="name" type="text" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900" for="SKU">SKU</label>
                        <input id="SKU" name="SKU" type="text" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900" for="price">Price (KSh)</label>
                        <input id="price" name="price" type="number" step="0.01" min="0" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5">
                    </div>
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900" for="description">Description</label>
                        <textarea id="description" name="description" rows="3" required
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg w-full p-2.5"></textarea>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" data-modal-hide="add-product" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-blue-600 rounded-lg">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.4.1/flowbite.min.js"></script>
</body>
</html>
