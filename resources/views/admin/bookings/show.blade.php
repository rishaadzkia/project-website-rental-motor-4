@extends('layouts.app')

@section('title', 'Detail Booking')

@section('content')

<div class="container">

    <h1>Detail Booking #{{ $booking->id }}</h1>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card">

        <h2>Informasi User</h2>

        <p>
            Nama:
            {{ $booking->user->name }}
        </p>

        <p>
            Email:
            {{ $booking->user->email }}
        </p>

        <hr>

        <h2>Informasi Motor</h2>

        <p>
            Motor:
            {{ $booking->motorcycle->name }}
        </p>

        <p>
            Plat:
            {{ $booking->motorcycle->plate_number }}
        </p>

        <hr>

        <p>
            Mulai:
            {{ $booking->start_date->format('d M Y') }}
        </p>

        <p>
            Selesai:
            {{ $booking->end_date->format('d M Y') }}
        </p>

        <p>
            Total Hari:
            {{ $booking->total_days }}
        </p>

        <p>
            Total Harga:
            Rp {{ number_format($booking->total_price, 0, ',', '.') }}
        </p>

        <hr>

        <form
            action="{{ route('admin.bookings.update', $booking) }}"
            method="POST"
        >

            @csrf
            @method('PATCH')

            <div class="form-group">

                <label for="status">
                    Status Booking
                </label>

                <select
                    name="status"
                    id="status"
                >

                    @foreach ([
                        'pending',
                        'confirmed',
                        'ongoing',
                        'completed',
                        'cancelled'
                    ] as $status)

                        <option
                            value="{{ $status }}"
                            @selected($booking->status === $status)
                        >
                            {{ ucfirst($status) }}
                        </option>

                    @endforeach

                </select>

            </div>

            <button type="submit">
                Update Status
            </button>

        </form>

        <br>

        <a href="{{ route('admin.bookings.index') }}">
            Kembali
        </a>

    </div>

</div>

@endsection