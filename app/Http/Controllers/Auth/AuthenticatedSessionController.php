<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * عرض صفحة تسجيل الدخول (هذه هي الدالة التي كانت مفقودة)
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * معالجة طلب تسجيل الدخول
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 1. التحقق من البيانات وتسجيل الدخول (مدمج في LoginRequest الخاص بـ Laravel Breeze)
        $request->authenticate();

        // 2. تجديد معرف الجلسة لأمان أعلى
        $request->session()->regenerate();

        // ✅ 3. السطر السحري: تحميل الأدوار والصلاحيات فوراً في نفس الجلسة
        $user = Auth::user();
        $user->load('roles.permissions');

        // 4. التوجيه إلى الصفحة المقصودة أو لوحة التحكم
        return redirect()->intended(route('dashboard'));
    }

    /**
     * تسجيل الخروج وإنهاء الجلسة (لإصلاح فشل اختبار logout)
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}