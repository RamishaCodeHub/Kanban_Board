<?php

namespace App\Http\Controllers;
use App\Models\Setting;

use Illuminate\Http\Request;
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
    $request->validate([
        'image' => 'required|image|mimes:jpeg,png,jpg,gif',
    ]);

    // Get the old image filename from the database
    $setting = Setting::where('key', 'background_image')->first();
    $oldImageFilename = $setting ? $setting->value : null;

    // Delete the old image (if it exists)
    if ($oldImageFilename) {
        Storage::disk('public')->delete('app/public/images/' . $oldImageFilename);
    }

    // Store the new image in the "value" column and in the "public/storage/images" folder
    if ($request->file()) {
        $fileName = time() . '_' . $request->image->getClientOriginalName();
        Storage::disk('public')->put('app/public/images/' . $fileName, File::get($request->file('image')));

        if (!$setting) {
            // If the setting record doesn't exist, create a new one
            $setting = new Setting(['key' => 'background_image']);
        }

        // Update the "value" column in the database
        $setting->value = $fileName;
        $setting->save();
    }

    return response()->json(['value' => $setting->value]);
}
    
    public function getBackgroundImage()
    {
        $setting = Setting::where('key', 'background_image')->first();
        $value = $setting ? $setting->value : '';
        return response()->json(['value' => $value]);
    }

}
