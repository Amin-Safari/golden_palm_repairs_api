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
        $tomorrow = Carbon::tomorrow();

        $devices = Device::whereBetween('delivered_real_at', [$today, $tomorrow])
            ->selectRaw('SUM(price) as total_price, SUM(repair_price) as total_repair_price')
            ->first();

        $income = ($devices->total_price ?? 0) - ($devices->total_repair_price ?? 0);

        return response()->json($income);
    }
}
