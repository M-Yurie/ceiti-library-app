@extends('layouts.dashboard')

@section('content')

<h1 class="text-2xl font-bold mb-6">Librarian Dashboard</h1>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">

    <div class="bg-white p-5 rounded-xl shadow">
        <h3 class="text-gray-500">Total Books</h3>
        <p class="text-2xl font-bold">{{ $totalBooks }}</p>
    </div>

    <div class="bg-white p-5 rounded-xl shadow">
        <h3 class="text-gray-500">Total Copies</h3>
        <p class="text-2xl font-bold">{{ $totalCopies }}</p>
    </div>

    <div class="bg-white p-5 rounded-xl shadow">
        <h3 class="text-gray-500">Active Loans</h3>
        <p class="text-2xl font-bold">{{ $activeLoans }}</p>
    </div>

</div>

@endsection
