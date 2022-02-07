<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'TRA' => [
        'registerURL' => 'https://vfd.tra.go.tz/api/vfdRegReq',
        'registerTestURL' => 'https://virtual.tra.go.tz/efdmsRctApi/api/vfdRegReq',
        'postInvoiceURL' => 'https://vfd.tra.go.tz/api/vfdRegReq',
        'postInvoiceTestURL' => 'https://virtual.tra.go.tz/efdmsRctApi/api/efdmsRctInfo',
        'TestInvoiceURL' => 'https://virtual.tra.go.tz/efdmsRctVerify',
        'TestTokenURL' => 'https://virtual.tra.go.tz/efdmsRctApi/api/vfdRegReq',
        'TokenURL' => 'https://virtual.tra.go.tz/efdmsRctApi/api/vfdRegReq',
        'ZReportInvoicesTest' => 'https://virtual.tra.go.tz/efdmsRctApi/api/vfdRegReq',
        'ZReportInvoices' => 'https://virtual.tra.go.tz/efdmsRctApi/api/vfdRegReq',
        'TIN' => '110781512',
        'CertKey' => '10TZ100705',
    ],

];
