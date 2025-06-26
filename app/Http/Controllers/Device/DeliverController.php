<?php

namespace App\Http\Controllers\Device;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class DeliverController extends Controller
{
    public function v_code(Request $request)
    {
        $v = Validator::make($request->all(),[
            'v_code'=>'required|exists:devices,verification_code'
        ]);
        if ($v->fails()){
            return response()->json($v->errors());
        }
        $d = Device::Where('verification_code',$v->c_code)->get();
        $code = rand(100000, 999999);
//        cach kardan yadet nare sms
        Cache::put($v, $code, 5);

        return response()->json($d,$code);
    }
    public function check_code(Request $request)
    {
        $v = Validator::make($request->all(),[
            'code'=>'required',
            'v_code'=>'required'
        ]);
        if ($v->fails()){
            return response()->json($v->errors());
        }
        $code_get = $request->code;
        $mobile = $request->v_code;
        $code = Cache::get($mobile);
        if ($code != $code_get) {
            return response()->json('verify code is not value');
        }
        return response()->json('success');
    }
}
