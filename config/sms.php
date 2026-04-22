<?php

/*
|--------------------------------------------------------------------------
| SMS / Broadnet API — Hardcoded Defaults
|--------------------------------------------------------------------------
| Priority:
|   1. otpsettings table (DB — admin-configurable)
|   2. config values below
|
| The OtpSetting model maps DB keys to config paths:
|   'sms_url'  → 'sms.jordan_api.endpoint'
|   'sms_user' → 'sms.jordan_api.user'
|   'sms_pass' → 'sms.jordan_api.pass'
|   'sms_sid'  → 'sms.jordan_api.sid'
|   'sms_type' → 'sms.jordan_api.type'
*/

return [

    'jordan_api' => [
        'endpoint' => env('SMS_URL',  'https://gwjo1s.broadnet.me:8443/websmpp/websms'),
        'user'     => env('SMS_USER', 'JbuyApp1'),
        'pass'     => env('SMS_PASS', '429J@NewY'),
        'sid'      => env('SMS_SID',  'Jbuy.App'),
        'type'     => env('SMS_TYPE', 4),
    ],

    'otp_ttl_minutes' => env('OTP_TTL', 5),
    'otp_length'      => env('OTP_LEN', 6),

];