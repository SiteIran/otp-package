<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Carbon\Carbon;

class PasswordReset extends Model
{
    public $timestamps = false;

    use HasFactory;

    protected $fillable = [
        'mobile',
        'email',
        'token',
        'created_at'
    ];

    public static function makeOtpToken($user, $type){
        $timestamps = false;
        $otp = null;
        if($type === 'mobile') {

            // پاک کردن کدهای ارسالی قبلی برای این شماره تماس
            PasswordReset::where('mobile', $user->mobile)->delete();
            // ثبت رکورد جدید
            $otp = PasswordReset::create([
                'mobile' => $user->mobile,
                'token' => rand(1000, 9999),
                'created_at' => Carbon::now()
            ]);
        }else if($type === 'email'){

            // پاک کردن کدهای ارسالی قبلی برای این ایمیل
            PasswordReset::where('email', $user->email)->delete();
            // ثبت رکورد جدید
            $otp = PasswordReset::create([
                'email' => $user->email,
                'token' => rand(1000, 9999),
                'created_at' => Carbon::now()
            ]);
        }

        return $otp;
    }

    public static function check($mobile)
    {
        $found = PasswordReset::where('mobile', $mobile)->where('created_at', '>', now()->subMinutes(2) )->first();
        return !$found;
    }

    public static function checkEmail($mobile)
    {
        $found = PasswordReset::where('email', $mobile)->where('created_at', '>', now()->subMinutes(5) )->first();
        return !$found;
    }

}
