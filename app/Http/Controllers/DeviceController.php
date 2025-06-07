<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class DeviceController extends Controller
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function index()
    {
        $devices = Device::with(['user', 'description'])->get();
        return response()->json([
            'status' => 'success',
            'data' => $devices
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|regex:/^09[0-9]{9}$/',
            'name' => 'required|string|max:255',
            'description_id' => 'required|exists:device_descriptions,id'
            
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            $customerRole = Role::where('name', 'customer')->first();
            $user = User::create([
                'name' => $request->name,
                'mobile' => $request->mobile,
                'password' => bcrypt(str_random(8))
            ]);
            $user->assignRole($customerRole);
        }

        $device = Device::create([
            'user_id' => $user->id,
            'description_id' => $request->description_id
        ]);

        // ارسال پیامک ثبت دستگاه
        $device->load('description');
        $this->smsService->sendDeviceRegistered(
            $user->mobile,
            $user->name,
            $device->description->description
        );

        return response()->json([
            'status' => 'success',
            'message' => 'دستگاه با موفقیت ثبت شد',
            'data' => $device->load(['user', 'description'])
        ], 201);
    }

    public function update(Request $request, Device $device)
    {
        $validator = Validator::make($request->all(), [
            'description_id' => 'required|exists:device_descriptions,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $device->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'دستگاه با موفقیت بروزرسانی شد',
            'data' => $device->load(['user', 'description'])
        ]);
    }

    public function destroy(Device $device)
    {
        // ذخیره اطلاعات قبل از حذف برای ارسال پیامک
        $device->load(['user', 'description']);
        $user = $device->user;
        $description = $device->description->description;

        $device->delete();

        // ارسال پیامک حذف دستگاه
        $this->smsService->sendDeviceDeleted(
            $user->mobile,
            $user->name,
            $description
        );

        return response()->json([
            'status' => 'success',
            'message' => 'دستگاه با موفقیت حذف شد'
        ]);
    }

    public function getUserName(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|regex:/^09[0-9]{9}$/'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('mobile', $request->mobile)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'کاربری با این شماره موبایل یافت نشد'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'name' => $user->name
            ]
        ]);
    }

    public function markAsReady(Device $device)
    {
        $device->load(['user', 'description']);

        // ارسال پیامک آماده بودن دستگاه
        $this->smsService->sendDeviceReady(
            $device->user->mobile,
            $device->user->name,
            $device->description->description
        );

        return response()->json([
            'status' => 'success',
            'message' => 'وضعیت دستگاه به آماده تغییر کرد و پیامک ارسال شد'
        ]);
    }
}
