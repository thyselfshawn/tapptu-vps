<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function show()
    {
        $setting = Setting::firstOrCreate();
        return view('settings.show', compact('setting'));
    }

    public function edit()
    {
        $setting = Setting::firstOrCreate();
        return view('settings.edit', compact('setting'));
    }

    public function update(Request $request)
    {
        $setting = Setting::firstOrCreate();

        $validatedData = $request->validate([
            'wa_number' => 'required|string',
            'wa_instanceid' => 'required|string',
            'wa_accesstoken' => 'required|string',
            'stripe_publishable' => 'required|string',
            'stripe_secret' => 'required|string',
            'stripe_webhook_secret' => 'required|string',
        ]);

        $setting->update($validatedData);

        return redirect()->route('settings.show')->with('success', 'Settings updated successfully.');
    }
}