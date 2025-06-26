<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function check(Request $request)
    {
        $mobile = $request->get('mobile');
        $id_mobile = User::where('mobile', $mobile)->first();
        if ($id_mobile) {
            $name_user = User::find($id_mobile)->get('name');
            return response()->json($name_user);
        }
        return response()->json('mobile not found', 404);
    }

    public function Auth(Request $request)
    {
        $v = Validator::make($request->all(), [
            'mobile' => 'required|max:11'
        ]);
        if ($v->fails()) {
            return response()->json($v->errors(), 422);
        }
        $check = User::where('mobile', $v)->first();
        if (!$check) {
            return response()->json('mobile not found', 404);
        }
        $code = rand(100000, 999999);
//        cach kardan yadet nare sms
        Cache::put($v, $code, 5);
        return response()->json($v, $code);
    }

    public function login(Request $request)
    {
        $code_get = $request->code;
        $mobile = $request->mobile;
        $code = Cache::get($mobile);
        if ($code == $code_get) {
            $token = $mobile->user()->createToken($request->token_name);

            return response()->json($token->plainTextToken);
        }
        return response()->json('code not value');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json('log out successfully');
    }

    public function create_user(Request $request)
    {
        $v = Validator::make($request->all(), [
            'name' => 'required|max:200',
            'mobile' => 'required|unique:users,mobile',
            'role' => 'required'
        ]);
        if ($v->fails()) {
            return response()->json($v->errors());
        }
        $user = User::create($v);
        return response()->json($user);
    }

    public function search_user(Request $request)
    {
        $query = $request->input('user');
        $results = User::where('name', 'like', "%{$query}%")
            ->orWhere('mobile', 'like', "%{$query}%")
            ->orWhere('name', 'like', "%{$query}%")
            ->orWhere('role','like', "{$query}")
            ->get();
        return response()->json($results);
    }
}
