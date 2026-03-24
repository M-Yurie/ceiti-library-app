<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="flex">

    <!-- Sidebar -->
    <aside class="w-64 bg-white h-screen shadow-md p-5">
        <h2 class="text-xl font-bold mb-6">Library App</h2>

        <nav class="space-y-3">
            <a href="{{ route('dashboard') }}" class="block text-gray-700">Dashboard</a>
            <a href="{{ route('books.index') }}" class="block text-gray-700">Books</a>
            <a href="{{ route('favorites.index') }}" class="block text-gray-700">Favorites</a>
            @if(auth()->user()->isAdmin() || auth()->user()->isLibrarian())
                <a href="{{ route('loans.create') }}" class="block text-gray-700">Borrow Copy</a>
            @endif
            <a href="{{ route('profile.edit') }}" class="block text-gray-700">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="block text-gray-700">Log Out</button>
            </form>
        </nav>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-6">
        @yield('content')
    </main>

</div>

</body>
</html>
