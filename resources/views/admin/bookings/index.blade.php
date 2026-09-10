@extends('layouts.app')

@section('title', 'Manage Booking')

@section('content')

<div class="container">

    <h1>Manage Booking</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @foreach ($bookings as $booking)

        <div class="card">

            <h2>
                Booking #{{ $booking->id }}
            </h2>

            <p>
                User:
                {{ $booking->user->name }}
            </p>

            <p>
                Motor:
                {{ $booking->motorcycle->name }}
            </p>

            <p>
                Tanggal:
                {{ $booking->start_date->format('d M Y') }}
                -
                {{ $booking->end_date->format('d M Y') }}
            </p>

            <p>
                Total:
                Rp {{ number_format($booking->total_price, 0, ',', '.') }}
            </p>

            <p>
                Status:
                <strong>
                    {{ ucfirst($booking->status) }}
                </strong>
            </p>

            <a
                href="{{ route('admin.bookings.show', $booking) }}"
            >
                Detail
            </a>

        </div>

    @endforeach

    {{ $bookings->links() }}

</div>

@endsection