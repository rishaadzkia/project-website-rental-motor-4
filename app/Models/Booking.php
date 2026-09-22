<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'motorcycle_id',
        'start_date',
        'end_date',
        'pickup_time',
        'return_time',
        'total_days',
        'total_price',
        'status',
        'paid_at',
        'payment_method',
        'actual_pickup',
        'actual_return',
        'pickup_method',
        'pickup_address',
        'delivery_fee',
        'snap_token',
        'is_verified',
        'driver_name',
        'driver_phone',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date'    => 'datetime',
            'end_date'      => 'datetime',
            'paid_at'       => 'datetime',
            'actual_pickup' => 'datetime',
            'actual_return' => 'datetime',
            'total_days'    => 'integer',
            'total_price'   => 'decimal:2',
            'delivery_fee'  => 'decimal:2',
            'is_verified'   => 'boolean',
        ];
    }

    /**
     * Accessor untuk mendapatkan Total Harga Keseluruhan (Sewa + Ongkir)
     * Dipanggil dengan: $booking->grand_total
     */
    public function getGrandTotalAttribute(): float
    {
        $deliveryFee = ($this->pickup_method === 'delivery') ? (float) ($this->delivery_fee ?? 0) : 0;
        return (float) $this->total_price + $deliveryFee;
    }

    /**
     * Helper Kode Booking Unik
     * Contoh: #RENT-00022
     */
    public function getBookingCodeAttribute(): string
    {
        return '#RENT-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Accessor Tanggal & Jam Mulai Sewa Rapi
     * Contoh: "Senin, 12 Okt 2026 - 09:00 WIB"
     */
    public function getFormattedStartDatetimeAttribute(): string
    {
        if (!$this->start_date) return '-';
        $time = $this->pickup_time ? Carbon::parse($this->pickup_time)->format('H:i') : '09:00';
        return $this->start_date->translatedFormat('D, d M Y') . ' - ' . $time . ' WIB';
    }

    /**
     * Accessor Tanggal & Jam Selesai Sewa Rapi
     * Contoh: "Rabu, 14 Okt 2026 - 09:00 WIB"
     */
    public function getFormattedEndDatetimeAttribute(): string
    {
        if (!$this->end_date) return '-';
        $time = $this->return_time ? Carbon::parse($this->return_time)->format('H:i') : '09:00';
        return $this->end_date->translatedFormat('D, d M Y') . ' - ' . $time . ' WIB';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function motorcycle(): BelongsTo
    {
        return $this->belongsTo(Motorcycle::class);
    }
}