<?php

// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^09[0-9]{9}$/'
        ]);

        $code = rand(1000, 9999);
        $phone = $request->phone;
        
        Cache::put('verification_' . $phone, $code, now()->addMinutes(5));

        // اینجا باید سرویس SMS واقعی را فراخوانی کنید
        // SMS::send($phone, "کد تأیید شما: $code");

        return response()->json([
            'message' => 'کد تأیید ارسال شد',
            'code' => $code // فقط برای محیط توسعه
        ]);
    }

    public function verifyCode(Request $request)
    {
        $request->validate([
            'phone' => 'required|regex:/^09[0-9]{9}$/',
            'code' => 'required|numeric'
        ]);

        $phone = $request->phone;
        $code = $request->code;

        if (Cache::get('verification_' . $phone) == $code) {
            $user = User::firstOrCreate(['phone' => $phone]);

            // ایجاد توکن دسترسی
            $token = $user->createToken('api-token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user
            ], 200)->header('Content-Type', 'application/json');
        }

        return response()->json(['error' => 'کد تأیید نامعتبر است'], 401)->header('Content-Type', 'application/json');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'با موفقیت خارج شدید']);
    }
}
