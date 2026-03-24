@extends('layouts.dashboard')

@section('content')

<div class="max-w-4xl space-y-6">
    <div class="rounded bg-white p-6 shadow">
        <h1 class="mb-4 text-2xl font-bold">Borrow Book Copy</h1>

        @if(session('success'))
            <p class="mb-3 text-sm text-green-600">{{ session('success') }}</p>
        @endif

        @if(session('error'))
            <p class="mb-3 text-sm text-red-600">{{ session('error') }}</p>
        @endif

        <form method="POST" action="{{ route('loans.lookup') }}" class="flex flex-wrap items-end gap-3">
            @csrf

            <div class="flex-1">
                <label for="barcode" class="mb-2 block font-medium">Scan or enter barcode</label>
                <input
                    id="barcode"
                    name="barcode"
                    type="text"
                    value="{{ old('barcode') }}"
                    class="w-full rounded border-gray-300"
                    placeholder="Scan barcode and press Enter"
                    autofocus
                >
                @error('barcode')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="rounded bg-gray-800 px-4 py-2 text-white">Find Copy</button>
        </form>
    </div>

    @if($selectedCopy)
        <div class="rounded bg-white p-6 shadow">
            <h2 class="mb-4 text-xl font-semibold">Selected Copy</h2>

            <div class="space-y-1 text-gray-700">
                <p><span class="font-medium">Book:</span> {{ $selectedCopy->book->title }}</p>
                <p><span class="font-medium">Author:</span> {{ $selectedCopy->book->author }}</p>
                <p><span class="font-medium">Barcode:</span> {{ $selectedCopy->barcode }}</p>
                <p><span class="font-medium">Status:</span> {{ ucfirst($selectedCopy->status) }}</p>
            </div>

            @if($selectedCopy->loan)
                <p class="mt-4 text-sm text-red-600">
                    This copy is already borrowed by {{ $selectedCopy->loan->user->name }}.
                </p>
            @else
                <form method="POST" action="{{ route('loans.store', $selectedCopy) }}" class="mt-6 space-y-4">
                    @csrf

                    <div>
                        <label for="user-search" class="mb-2 block font-medium">Search student by name</label>
                        <input
                            id="user-search"
                            type="text"
                            class="w-full rounded border-gray-300"
                            placeholder="Type student name"
                            autocomplete="off"
                        >

                        <input type="hidden" name="user_id" id="selected-user-id" value="{{ old('user_id') }}">

                        <div id="selected-user-label" class="mt-2 text-sm text-gray-700"></div>

                        @error('user_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <div id="user-results" class="mt-3 space-y-2"></div>
                    </div>

                    <button type="submit" class="rounded bg-gray-800 px-4 py-2 text-white">
                        Confirm Borrow
                    </button>
                </form>
            @endif
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const input = document.getElementById('user-search');
        const results = document.getElementById('user-results');
        const userId = document.getElementById('selected-user-id');
        const label = document.getElementById('selected-user-label');

        if (!input || !results || !userId || !label) {
            return;
        }

        const selectUser = (user) => {
            userId.value = user.id;
            label.textContent = `Selected student: ${user.name}`;
            results.innerHTML = '';
            input.value = user.name;
        };

        input.addEventListener('input', async () => {
            const term = input.value.trim();
            userId.value = '';
            label.textContent = '';

            if (term.length < 2) {
                results.innerHTML = '';
                return;
            }

            const response = await fetch(`{{ route('loans.users.search') }}?q=${encodeURIComponent(term)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            const users = await response.json();
            results.innerHTML = '';

            if (!users.length) {
                results.innerHTML = '<p class="text-sm text-gray-500">No matching students found.</p>';
                return;
            }

            users.forEach((user) => {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'block w-full rounded border px-3 py-2 text-left text-sm hover:bg-gray-50';
                button.textContent = user.name;
                button.addEventListener('click', () => selectUser(user));
                results.appendChild(button);
            });
        });
    });
</script>

@endsection
