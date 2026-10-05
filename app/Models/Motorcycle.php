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
        'description',
        'image',
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
        // 1. Jika Admin set manual ke maintenance, selalu kembalikan maintenance
        if ($this->status === 'maintenance') {
            return 'maintenance';
        }

        // 2. Ambil waktu acuan pengecekan (default waktu sekarang)
        $checkTime = request('rental_time') 
            ? Carbon::parse(request('rental_time')) 
            : Carbon::now();

        $checkDate = $checkTime->toDateString(); // Contoh: '2026-10-20'

        // 3. Cek apakah ada transaksi sewa aktif pada tanggal tersebut
        $isRented = $this->bookings()
            // Masukkan semua status booking yang dianggap sah/terkonfirmasi
            ->whereIn('status', ['confirmed', 'paid', 'approved', 'active', 'ongoing', 'terkonfirmasi'])
            ->whereDate('start_date', '<=', $checkDate)
            ->whereDate('end_date', '>=', $checkDate)
            ->exists();

        return $isRented ? 'rented' : 'available';
    }
}