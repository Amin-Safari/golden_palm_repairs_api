<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\UserMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserMessageController extends Controller
{
    public function index() 
    {
        $UserMessage = UserMessage::all();
        return response()->json($UserMessage);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:225',
            'email' => 'required|email|max:225',
            'phone' => 'required|size:11',
            'subject' => 'required|max:225',
            'message' => 'required|max:65535',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $UserMessage = UserMessage::create($validator);
        return response()->json($UserMessage);
    }
}
