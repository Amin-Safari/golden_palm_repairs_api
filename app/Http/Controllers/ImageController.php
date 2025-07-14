<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:1024'
        ]);
        if ($validator->fails()) {
            return $this->fail($validator);
        }
        $file = $request->file('image');
        $timestamp = Carbon::now()->format('Ymd_Hisu');
        $extension = $file->getClientOriginalExtension();
        $filename = "{$timestamp}_" . Str::random(8) . ".{$extension}";
        $path = $file->move(public_path('images'), $filename);
        $image = Image::create([
            'path' => 'images/' . $filename,
        ]);
        return $this->add('تصویر', $image);
    }
    public function destroy($path)
    {
        if (!file_exists(public_path($path))) {
            return response()->json([
                'message' => 'فایل مورد نظر یافت نشد',
                'path' => $path
            ], 404);
        }
        unlink(public_path($path));
        $image = Image::where('path', $path)->first();
        if ($image) {
            $image->delete();
        }
        return $this->delete('تصویر');
    }
}
