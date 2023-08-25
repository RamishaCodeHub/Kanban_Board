<?php

namespace App\Http\Controllers;
use App\Models\Setting;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Storage;
use File;

class SettingController extends Controller
{
    public function setBackgroundColor(Request $request)
    { 
        $value = $request->input('value');

        $setting = Setting::firstOrCreate(['key' => 'background_color']);
        $setting->value = $value;
        $setting->save();

        return response()->json(['value' => $setting->value]);
    }

    public function getBackgroundColor()
    {
        $setting = Setting::where('key', 'background_color')->first();
        $value = $setting ? $setting->value : '';
        return response()->json(['value' => $value]);
    }

    public function setBackgroundImage(Request $request)
    {
        $value = $request->input('value');

        $setting = Setting::firstOrCreate(['key' => 'background_image']);
        $setting->value = $value;
        $setting->save();

        return response()->json(['value' => $setting->value]);
    }

    public function getBackgroundImage()
    {
        $setting = Setting::where('key', 'background_image')->first();
        $value = $setting ? $setting->value : '';
        return response()->json(['value' => $value]);
    }

}
