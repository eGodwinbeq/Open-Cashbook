<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings');
    }

    public function updateCurrency(Request $request)
    {
        $request->validate([
            'currency_symbol' => 'required|string|max:10',
        ]);

        auth()->user()->update([
            'currency_symbol' => $request->currency_symbol,
        ]);

        return back()->with('success', 'Currency symbol updated successfully.');
    }

    public function completeOnboarding(Request $request)
    {
        $request->validate([
            'currency_symbol' => 'required|string|max:10',
        ]);

        auth()->user()->update([
            'currency_symbol' => $request->currency_symbol,
            'onboarding_completed' => true,
        ]);

        return back()->with('success', 'Welcome! Your currency has been set.');
    }
}
