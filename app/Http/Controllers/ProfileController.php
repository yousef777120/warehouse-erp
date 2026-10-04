<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * عرض صفحة الملف الشخصي.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        // تحميل أدوار المستخدم عند استخدام Spatie Permission
        $user->loadMissing('roles');

        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * تحديث البيانات الشخصية.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        // إذا تغير البريد الإلكتروني، إلغاء حالة التحقق السابقة
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')
            ->with('success', 'تم تحديث البيانات الشخصية بنجاح');
    }

    /**
     * تغيير كلمة المرور.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'current_password.required' => 'يرجى إدخال كلمة المرور الحالية.',
            'current_password.current_password' => 'كلمة المرور الحالية غير صحيحة.',
            'password.required' => 'يرجى إدخال كلمة المرور الجديدة.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
            'password.min' => 'يجب أن تكون كلمة المرور الجديدة 8 أحرف على الأقل.',
        ]);

        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return Redirect::route('profile.edit')
            ->with('success', 'تم تغيير كلمة المرور بنجاح');
    }

    /**
     * حذف الحساب.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ], [
            'password.required' => 'يرجى إدخال كلمة المرور.',
            'password.current_password' => 'كلمة المرور غير صحيحة.',
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}