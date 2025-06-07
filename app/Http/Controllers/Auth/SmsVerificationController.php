<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class SmsVerificationController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|regex:/^09[0-9]{9}$/'
        ]);

        $code = rand(100000, 999999);
        $phoneNumber = $request->phone_number;

        // ذخیره کد در کش برای 5 دقیقه
        Cache::put('verification_code_' . $phoneNumber, $code, now()->addMinutes(5));

        // دریافت نام کاربر اگر وجود داشته باشد
        $user = User::where('mobile', $phoneNumber)->first();
        $name = $user ? $user->name : 'کاربر';

        $result = $this->smsService->sendVerificationCode($phoneNumber, $code, $name);

        if ($result['success']) {
            return response()->json([
                'message' => 'کد تایید با موفقیت ارسال شد'
            ]);
        }

        return response()->json([
            'message' => $result['message']
        ], 500);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|regex:/^09[0-9]{9}$/',
            'code' => 'required|numeric|digits:6'
        ]);

        $phoneNumber = $request->phone_number;
        $code = $request->code;

        $cachedCode = Cache::get('verification_code_' . $phoneNumber);

        if (!$cachedCode) {
            return response()->json([
                'message' => 'کد تایید منقضی شده است'
            ], 400);
        }

        if ($cachedCode != $code) {
            return response()->json([
                'message' => 'کد تایید نامعتبر است'
            ], 400);
        }

        // پاک کردن کد از کش
        Cache::forget('verification_code_' . $phoneNumber);

        return response()->json([
            'message' => 'کد تایید با موفقیت تایید شد'
        ]);
    }
}
