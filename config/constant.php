<?php
//global constants define here
return [
    'responseStatus' => [
        'success' => [
            'code' => 0,
            'errMsg' => 'Success',
        ],
        'unknownErr' => [
            'code' => 1,
            'errMsg' => 'Unknown error',
        ],
        'authenticateErr' => [
            'code' => 2,
            'errMsg' => 'Username or password is not correct',
        ],
        'validateErr' => [
            'code' => 3,
            'errMsg' => 'Params not currect',
        ]

    ],

    'rolesBitmap' => [
        'Passenger'     => 0,
        'Driver'        => 1,
        'Administrator' => 2,
    ],

    'vehicleStatus' => [
        'Ready'  => 0,
        'Broken' => 1,
        'Fixing' => 2,
    ],

    'requestStatus' => [
        'Requesting' =>1,
        'Finished'   =>2,
        'Canceled'   =>3,
    ],

    'price' => [
        'baseCharge'    => 1.40,
        'costPerKM'     => 1.60,
        'costPerMin'    => 0.3,
        'costMinimum'   => 5.00,
        'cancellation'  => 10.00,
    ],

    'unknownErrResponse' => [
        'status' => 1 ,
        'errMsg' => 'unknownErr' ,
    ],
    'successResponse' => [
        'status' => 0 ,
        'errMsg' => 'success' ,
    ],

    'tripStatus' => [
        'notCatch'  => 0,
        'onTrip'    => 1,
        'arrived'   => 2,
        'finished'  => 3,
    ],

    'driverStatus' => [
        'offline' => 0,
        'online'  => 1,
        'onTrip'  => 2,
    ],

    'maxReviewRate' => 5,

    'registerStatus' => [
        'reviewing' => 0,
        'approved'  => 1,
        'declined'  => 2,
        'draft'     => 9,
    ],

    'driverRegisterOperate' => [
        'approve' => 1,
        'decline' => 0,
    ],

];