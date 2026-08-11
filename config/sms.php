<?php

return [
    'driver' => env('SMS_DRIVER', 'log'),

    'username' => env('SMS_AFRICASTALKING_USERNAME', ''),
    'api_key' => env('SMS_AFRICASTALKING_API_KEY', ''),
    'sender_id' => env('SMS_SENDER_ID', 'BodaLoan'),
    'environment' => env('SMS_ENVIRONMENT', 'sandbox'),
];
