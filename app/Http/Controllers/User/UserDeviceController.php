<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDeviceController extends Controller
{
    public function customerDevice()
    {
        $userid = Auth::guard('sanctum')->user()->id;
        $device = Device::where('customer_id', $userid)->get();
        return response()->json($device);
    }

    public function technicianDevice()
    {
        $userid = Auth::guard('sanctum')->user()->id;
        $device = Device::where('technician_id', $userid)->get();
        return response()->json($device);
    }
}
