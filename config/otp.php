<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OTP Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may specify the settings for your OTP service.
    |
    */

    'otp_expiration' => env('OTP_EXPIRATION', 5), // in minutes
    'otp_length' => env('OTP_LENGTH', 6), // length of OTP code
    'otp_type' => env('OTP_TYPE', 'sms'), // sms or email

    'sms_gateway' => env('SMS_GATEWAY', 'twilio'),
    'email_service' => env('EMAIL_SERVICE', 'smtp'),
    'otp_template' => env('OTP_TEMPLATE', 'Your OTP code is: :code'),

];
