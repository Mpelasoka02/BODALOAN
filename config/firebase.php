<?php

return [

    'project_id' => env('FIREBASE_PROJECT_ID', ''),

    'service_account_path' => env('FIREBASE_SERVICE_ACCOUNT_PATH', storage_path('app/bodaloan-firebase-adminsdk-fbsvc-929e63bff3.json')),

    'database_url' => env('FIREBASE_DATABASE_URL', ''),

    'api_key' => env('FIREBASE_API_KEY', ''),

    'fcm_enabled' => env('FIREBASE_FCM_ENABLED', false),

    'realtime_db_enabled' => env('FIREBASE_REALTIME_DB_ENABLED', false),

    'fcm' => [
        'sender_id' => env('FIREBASE_FCM_SENDER_ID', ''),
    ],

];
