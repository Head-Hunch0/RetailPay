<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RetailPay</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.4.1/flowbite.min.css" rel="stylesheet" />
</head>
<body class="bg-gray-50 text-gray-800">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md bg-white border border-gray-600 rounded-lg shadow-xl p-12">
            <h1 class="text-2xl font-semibold mb-2">Welcome back</h1>
            <p class="text-sm text-gray-500 mb-6">Sign in to your RetailPay account</p>

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf

                @error('email')
                    <p class="my-2 text-sm flex text-red-600">{{ $message }}</p>
                @enderror

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900" for="email">Email</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    
                </div>

                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900" for="password">Password</label>
                    <input type="password" id="password" name="password" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>

                <button type="submit" class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Sign in
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.4.1/flowbite.min.js"></script>
</body>
</html>
