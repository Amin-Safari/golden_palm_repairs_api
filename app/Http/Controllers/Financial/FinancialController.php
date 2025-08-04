<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Device\DeviceController;
use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FinancialController extends Controller
{
    public function incomeToday()
    {
        $today = Carbon::today();

        $devices = Device::whereDate('delivered_real_at', $today)
            ->selectRaw('COALESCE(SUM(price), 0) as total_price,
                    COALESCE(SUM(repair_price), 0) as total_repair_price')
            ->first();

        $income = $devices->total_price - $devices->total_repair_price;

        return response()->json([
            'income' => $income,
            'total_sales' => $devices->total_price,
            'total_repair_costs' => $devices->total_repair_price,
            'date' => $today->toDateString()
        ]);
    }

    public function incomeThisMonth()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $devices = Device::whereBetween('delivered_real_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('SUM(price) as total_price, SUM(repair_price) as total_repair_price')
            ->first();

        $income = ($devices->total_price ?? 0) - ($devices->total_repair_price ?? 0);

        return response()->json($income);
    }

    public function incomeThisYear()
    {
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();

        $devices = Device::whereBetween('delivered_real_at', [$startOfYear, $endOfYear])
            ->selectRaw('SUM(price) as total_price, SUM(repair_price) as total_repair_price')
            ->first();

        $income = ($devices->total_price ?? 0) - ($devices->total_repair_price ?? 0);

        return response()->json($income);
    }

    public function technicianIncome(Request $request)
    {
        // دریافت کاربر از توکن
        $user = auth()->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // برای روز
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        $dailyIncome = Device::where('technician_id', $user->id)
            ->whereBetween('delivered_real_at', [$today, $tomorrow])
            ->sum('repair');

        // برای ماه
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $monthlyIncome = Device::where('technician_id', $user->id)
            ->whereBetween('delivered_real_at', [$startOfMonth, $endOfMonth])
            ->sum('repair');

        // برای سال
        $startOfYear = Carbon::now()->startOfYear();
        $endOfYear = Carbon::now()->endOfYear();
        $yearlyIncome = Device::where('technician_id', $user->id)
            ->whereBetween('delivered_real_at', [$startOfYear, $endOfYear])
            ->sum('repair');

        return response()->json([
            'daily_income' => $dailyIncome,
            'monthly_income' => $monthlyIncome,
            'yearly_income' => $yearlyIncome,
        ]);
    }
}
