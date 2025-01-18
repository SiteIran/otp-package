<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\EmailService; // سرویس ایمیل
use App\Models\Services\SmsService; // سرویس SMS

class AuthController extends Controller
{
    // ثبت نام با ایمیل
    public function registerByEmail(Request $request) {
        // اعتبارسنجی فیلدهای ورودی
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|confirmed'
        ]);

        // ایجاد کاربر جدید
        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password'])
        ]);

        // ایجاد توکن برای کاربر
        $token = $user->createToken('myapptoken')->plainTextToken;

        // پاسخ به کلاینت
        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    // ورود با ایمیل
    public function loginByEmail(Request $request) {
        // اعتبارسنجی فیلدهای ورودی
        $fields = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        // بررسی وجود کاربر با ایمیل وارد شده
        $user = User::where('email', $fields['email'])->first();

        // بررسی صحت رمز عبور
        if(!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'اعتبارسنجی ناموفق'
            ], 401);
        }

        // ایجاد توکن برای کاربر
        $token = $user->createToken('myapptoken')->plainTextToken;

        // پاسخ به کلاینت
        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    // احراز هویت با ایمیل (ارسال کد OTP)
    public function authEmail(Request $request) {
        // اعتبارسنجی فیلدهای ورودی
        $fields = $request->validate([
            'email' => 'required|string|unique:users,email',
        ]);

        // بررسی وضعیت ارسال مجدد کد OTP
        $check = PasswordReset::checkEmail($request->email);
        if ($check) {
            // ایجاد کد OTP
            $otp = PasswordReset::makeOtpToken($request, 'email');

            // ارسال ایمیل حاوی کد OTP
            $emailClass = new EmailService();
            $parameters = [
                'otp' => $otp->token
            ];
            $response = $emailClass->Send($request->email, $parameters);

            if($response === 200) {
                return response(['message' => 'کد تایید به ایمیل شما ارسال شد.'], 201);
            } else {
                PasswordReset::where('email', $request->email)->delete();
                return response(['message' => 'خطا در ارسال ایمیل'], 500);
            }
        } else {
            return response(['message' => 'به منظور ارسال مجدد کد به مدت 2 دقیقه منتظر بمانید.'], 401);
        }
    }

    // احراز هویت با موبایل (ارسال کد OTP)
    public function authMobile(Request $request) {
        // اعتبارسنجی فیلدهای ورودی
        $fields = $request->validate([
            'mobile' => 'required|string|digits:11',
        ]);

        // بررسی وضعیت ارسال مجدد کد OTP
        $check = PasswordReset::check($request->mobile);
        if ($check) {
            // ایجاد کد OTP
            $otp = PasswordReset::makeOtpToken($request, 'mobile');

            // ارسال SMS حاوی کد OTP
            $smsClass = new SmsService();
            $parameters = [
                ['name' => 'OTP', 'value' => $otp->token],
                ['name' => 'WEBSITE', 'value' => 'siteiran.com']
            ];
            $response = $smsClass->Send($request->mobile, '414932', $parameters);

            if($response === 200) {
                return response(['message' => 'کد تایید به موبایل شما ارسال شد.'], 201);
            } else {
                PasswordReset::where('mobile', $request->mobile)->delete();
                return response(['message' => 'خطا در ارسال SMS'], 500);
            }
        } else {
            return response(['message' => 'به منظور ارسال مجدد کد به مدت 2 دقیقه منتظر بمانید.'], 401);
        }
    }

    // تایید کد OTP ارسال شده به موبایل
    public function verifyMobile(Request $request) {
        // اعتبارسنجی فیلدهای ورودی
        $request->validate([
            'otpCode' => 'required',
        ]);

        // بررسی وجود کد OTP در دیتابیس
        $found = PasswordReset::where('token', $request->otpCode)->first();
        if ($found) {
            // بررسی انقضای زمان کد OTP
            $check = PasswordReset::check($found->mobile);
            if ($check) {
                return response(['message' => 'زمان استفاده از کد تایید منقضی گردیده است'], 401);
            }

            // بررسی وجود کاربر با موبایل وارد شده
            $userdata = User::where('mobile', $found->mobile)->first();
            if ($userdata === null) {
                // اگر کاربر وجود نداشت، ثبت نام شود
                $user = User::create([
                    'mobile' => $found->mobile,
                ]);
                $token = $user->createToken('myapptoken')->plainTextToken;
                $response = [
                    'user' => $user,
                    'token' => $token
                ];
            } else {
                // اگر کاربر وجود داشت، ورود انجام شود
                $token = $userdata->createToken('myapptoken')->plainTextToken;
                $response = [
                    'user' => $userdata,
                    'token' => $token
                ];
            }

            // حذف کد OTP از دیتابیس پس از استفاده
            PasswordReset::where('mobile', $found->mobile)->delete();
            return response($response, 201);
        } else {
            return response(['message' => 'کد وارد شده صحیح نمی باشد'], 401);
        }
    }

    // خروج کاربر
    public function logout(Request $request) {
        // حذف توکن‌های کاربر
        auth()->user()->tokens()->delete();

        return [
            'message' => 'خروج موفقیت‌آمیز بود'
        ];
    }

    // دریافت اطلاعات کاربر
    public function getUserInfo(Request $request) {
        // دریافت توکن از هدر درخواست
        $token = $request->header('Authorization');

        // دریافت اطلاعات کاربر با استفاده از توکن
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            return response()->json($user);
        } else {
            return response()->json(['message' => 'توکن معتبر نیست'], 401);
        }
    }
}