<?php

/*
|--------------------------------------------------------------------------
| SMS / Broadnet API — Hardcoded Defaults
|--------------------------------------------------------------------------
| These values are used when the corresponding key in the `settings` table
| is null or empty. The get_setting() helper enforces this priority:
|
|   1. settings.value (database / admin-configurable)
|   2. config('sms.*')  ← you are here
|
| Change these constants only for a fresh deployment.
| Per-client overrides should be done via the Admin → API Settings page.
*/

return [

    'url'  => env('SMS_URL',  'https://gwjo1s.broadnet.me:8443/websmpp/websms'),
    'user' => env('SMS_USER', 'JbuyApp1'),
    'pass' => env('SMS_PASS', '429J@NewY'),
    'sid'  => env('SMS_SID',  'Jbuy.App'),
    'type' => env('SMS_TYPE', 4),   // 4 = Unicode (Arabic support)

    /*
     * OTP behaviour
     */
    'otp_ttl_minutes' => env('OTP_TTL', 5),   // how long the code is valid
    'otp_length'      => env('OTP_LEN', 6),   // digits in the OTP code
];