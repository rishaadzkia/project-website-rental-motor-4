<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Motorcycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'model',
        'plate_number',
        'year',
        'price_per_day',
        'tank_capacity',
        'status', 
        'maintenance_requested_at',
        'description',
        'image',
    ];

    protected $casts = [
        'maintenance_requested_at' => 'datetime',
    ];

    /**
     * Relasi ke tabel Bookings
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Accessor untuk mendapatkan status aktual berdasarkan tanggal
     */
    public function getComputedStatusAttribute(): string
    {
        // 1. Jika Admin set manual ke maintenance atau pending_maintenance, selalu prioritaskan status ini
        if (in_array($this->status, ['maintenance', 'pending_maintenance'])) {
            return $this->status;
        }

        // 2. Ambil waktu acuan pengecekan (default waktu sekarang)
        $checkTime = request('rental_time') 
            ? Carbon::parse(request('rental_time')) 
            : Carbon::now();

        $checkDate = $checkTime->toDateString();

        // 3. Cek apakah ada transaksi sewa aktif pada tanggal tersebut
        $isRented = $this->bookings()
            ->whereIn('status', ['confirmed', 'paid', 'approved', 'active', 'ongoing', 'terkonfirmasi'])
            ->whereDate('start_date', '<=', $checkDate)
            ->whereDate('end_date', '>=', $checkDate)
            ->exists();

        return $isRented ? 'rented' : 'available';
    }
}