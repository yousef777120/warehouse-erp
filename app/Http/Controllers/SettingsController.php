<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    /**
     * عرض صفحة الإعدادات.
     */
    public function edit(Request $request): View
    {
        return view('settings.edit');
    }

    /**
     * حفظ الإعدادات.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'language' => ['required', 'string', 'in:ar,en'],
            'timezone' => ['required', 'string'],
        ], [
            'language.required' => 'يرجى اختيار اللغة.',
            'language.in' => 'اللغة المختارة غير صحيحة.',
            'timezone.required' => 'يرجى اختيار المنطقة الزمنية.',
        ]);

        // حالياً نحفظ الإعدادات في الجلسة.
        // ويمكن لاحقاً ربطها بجدول settings في قاعدة البيانات.

        session([
            'app_language' => $request->language,
            'app_timezone' => $request->timezone,
        ]);

        return redirect()
            ->route('settings.edit')
            ->with('success', 'تم حفظ الإعدادات بنجاح.');
    }
}