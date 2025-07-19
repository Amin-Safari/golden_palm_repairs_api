<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\smsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|size:11'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json([
            'name' => $user->name,
            'mobile' => $user->mobile
        ]);
    }

    public function auth(Request $request,smsService $smsService)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|size:11'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('mobile', $request->mobile)->first();
        $name = User::where('mobile', $request->mobile)->value('name');

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $code = rand(100000, 999999);
        $expiresAt = now()->addMinutes(5);

        Cache::put('auth_code_' . $request->mobile, $code, 5);

        $text = ' سلام '. $name.' عزیز '."\n"."کد ورود شما:"."\"".$code."\""."\n"."تعمیرات نخل طلایی";
//        $mas=$smsService->sendSms($request->mobile,$text);
        return response()->json([
            'message' => $code,
            'expires_at' => $expiresAt->toDateTimeString()
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|string|size:11',
            'code' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cachedCode = Cache::get('auth_code_' . $request->mobile);

        if ($cachedCode != $request->code) {
            return response()->json(['message' => 'Invalid verification code'], 401);
        }

        $user = User::where('mobile', $request->mobile)->firstOrFail();

        // پاک کردن کد استفاده شده از کش
        Cache::forget('auth_code_' . $request->mobile);

        // ایجاد توکن دسترسی
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function createUser(Request $request,smsService $smsService)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:200',
            'mobile' => 'required|string|size:11|unique:users,mobile',
            'role' => 'required|intiger' // مثال برای نقش‌های مجاز
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $text = ' سلام '. $request->name.' عزیز '."\n"."شما با موفقیت در سامانه ثبت شدید"."\n"."تعمیرات نخل طلایی";
        $mas=$smsService->sendSms($request->mobile,$text);

        $user = User::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'role' => $request->role
        ]);

        return response()->json([
            'message' => $mas,
            'user' => $user
        ], 201);
    }

    public function searchUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $users = User::where('name', 'like', "%{$request->query}%")
            ->orWhere('mobile', 'like', "%{$request->query}%")
            ->orWhere('role', $request->query)
            ->get();

        return response()->json($users);
    }
    public function technician()
    {
        $technician = User::where('role',1)->orWhere('role', 2)->get();
        return response()->json($technician);
    }
}
