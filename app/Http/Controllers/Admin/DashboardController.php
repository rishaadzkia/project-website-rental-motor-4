<?php

namespace App\Http\Controllers\Admin;

use App\Models\Booking;
use App\Models\Motorcycle;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $now = now();
        $currentMonth = $now->copy()->startOfMonth();
        $firstMonth = $currentMonth->copy()->subMonths(5);

        $revenueByMonth = Booking::query()
            ->whereNotNull('paid_at')
            ->whereBetween('paid_at', [$firstMonth, $now])
            ->get(['paid_at', 'total_price', 'pickup_method', 'delivery_fee'])
            ->groupBy(fn (Booking $booking): string => $booking->paid_at->format('Y-m'))
            ->map(fn ($bookings): float => $bookings->sum(
                fn (Booking $booking): float => $booking->grand_total
            ));

        $revenueChart = collect(range(0, 5))
            ->map(function (int $offset) use ($currentMonth, $revenueByMonth): array {
                $month = $currentMonth->copy()->subMonths(5 - $offset);
                $key = $month->format('Y-m');

                return [
                    'label' => $month->locale('id')->translatedFormat('M'),
                    'revenue' => round((float) ($revenueByMonth->get($key) ?? 0)),
                ];
            });

        $topMotorcycles = Motorcycle::query()
            ->withCount([
                'bookings as orders_count' => fn (Builder $query) => $query->where('status', '!=', 'cancelled'),
            ])
            ->whereHas('bookings', fn (Builder $query) => $query->where('status', '!=', 'cancelled'))
            ->orderByDesc('orders_count')
            ->orderBy('name')
            ->limit(5)
            ->get();

        $recentBookings = Booking::query()
            ->with(['user', 'motorcycle'])
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.dashboard', [
            'totalBookings' => Booking::count(),
            'pendingBookings' => Booking::where('status', 'pending')->count(),
            'currentMonthRevenue' => $revenueByMonth->get($currentMonth->format('Y-m'), 0),
            'revenueChart' => $revenueChart,
            'topMotorcycles' => $topMotorcycles,
            'recentBookings' => $recentBookings,
        ]);
    }
}