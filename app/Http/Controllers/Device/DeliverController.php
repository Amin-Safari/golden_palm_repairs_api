<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class DeliverController extends Controller
{
    public function v_code(Request $request, SmsService $smsService)
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
        $user = User::where('id', $device->customer_id)->first();
        $mobile = $user->mobile;
        $name = $user->name;
        $code = rand(100000, 999999);
        $text = ' سلام ' . $name . ' عزیز ' . "\n" . "کد احراز هویت  شما:" . "\"" . $code . "\"" . "\n" . "خدمات نخل طلایی";
        $mas = $smsService->sendSms($mobile, $text);

        Cache::put($request->v_code, $code, 300);

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

        $code = Cache::get($validator->v_code);

        if ($code != $request->code) {
            return response()->json(['error' => 'Invalid verification code'], 401);
        }
        $device->update(['status' => 'delivered']);
        // پاک کردن کد از کش پس از استفاده موفق
        Cache::forget($device->mobile);

        return response()->json(['message' => 'Verification successful']);
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'v_code'=>'required|string|exists:devices,verification_code',
            'user_code'=>'required|string'
        ]);
        if ($validator->fails()){
            return response()->json($validator->errors(),422);
        }
        $cachedCode = Cache::get($request->v_code);
        if ($cachedCode && $cachedCode == $request->user_code){
            $device = Device::where('verification_code',$request->v_code)->first();
            if ($device){
                $device->update(['status'=>'delivered']);
                Cache::forget($request->v_code);
                return response()->json(['message'=>'کد تایید صحیح است و وضعیت دستگاه به "تحویل داده شده"تغییر یافت']);
            }
        }


        return response()->json(['message'=>'کد وارد شده صحیح نیست'],401);
    }
}
