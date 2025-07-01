<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StatusController extends Controller
{
    public function index()
    {
        $stats = Status::Where('id',1)->get('status');
        return response()->json($stats);
    }
    public function update(Request $request)
    {
        $v = Validator::make($request->all(),[
            'status'=>'required|boolean'
        ]);
        if ($v->fails()){
            return response()->json($v->errors());
        }
        Status::updated($request->all());
        return response()->json(['message'=>'success']);
    }
}
