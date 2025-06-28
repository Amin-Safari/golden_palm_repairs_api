<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class DeliverController extends Controller
{
    public function v_code(Request $request,SmsService $smsService)
    {
        $validator = Validator::make($request->all(), [
            'v_code' => 'required|exists:devices,verification_code'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $device = Device::where('verification_code', $request->v_code)->first();

        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        $code = rand(100000, 999999);
        $text = ' سلام '. $name.' عزیز '."\n"."کد احراز هویت  شما:"."\"".$code."\""."\n"."تعمیرات نخل طلایی";
        $mas=$smsService->sendSms($request->mobile,$text);

        Cache::put($device->mobile, $code, 300); // 5 دقیقه

        return response()->json([
            'message' => $mas,
            'device' => $device,
        ]);
    }

    public function check_code(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'v_code' => 'required|exists:devices,verification_code'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $device = Device::where('verification_code', $request->v_code)->first();

        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }

        $code = Cache::get($device->mobile);

        if ($code != $request->code) {
            return response()->json(['error' => 'Invalid verification code'], 401);
        }

        // پاک کردن کد از کش پس از استفاده موفق
        Cache::forget($device->mobile);

        return response()->json(['message' => 'Verification successful']);
    }
}
