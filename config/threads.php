<?php

declare(strict_types=1);

return [
    'route_prefix' => 'api',
    'middleware' => ['api'],
    'user_model' => 'App\Models\User',

    'notifications' => [
        'comment_created' => [
            'sms' => [
                'enabled' => false,
                'message' => 'You have a new comment from :commenter_name',
            ],
            'database' => [
                'enabled' => false,
                'message' => 'You have a new comment from :commenter_name',
            ],
            'email' => [
                'enabled' => true,
                'subject' => 'New Comment',
                'message' => 'You have a new comment from :commenter_name',
            ],
            'push' => [
                'enabled' => false,
                'message' => 'You have a new comment from :commenter_name',
            ],
        ],
        'commenter_blocked' => [
            'sms' => [
                'enabled' => false,
                'message' => 'You have been blocked by :blocker_name',
            ],
            'database' => [
                'enabled' => false,
                'message' => 'You have been blocked by :blocker_name',
            ],
            'email' => [
                'enabled' => true,
                'subject' => 'You have been blocked',
                'message' => 'You have been blocked by :blocker_name',
            ],
            'push' => [
                'enabled' => false,
                'message' => 'You have been blocked by :blocker_name',
            ],
        ],
        'commenter_unblocked' => [
            'sms' => [
                'enabled' => false,
                'message' => 'You have been unblocked by :unblocker_name',
            ],
            'database' => [
                'enabled' => false,
                'message' => 'You have been unblocked by :unblocker_name',
            ],
            'email' => [
                'enabled' => true,
                'subject' => 'You have been unblocked',
                'message' => 'You have been unblocked by :unblocker_name',
            ],
            'push' => [
                'enabled' => false,
                'message' => 'You have been unblocked by :unblocker_name',
            ],
        ],
        'like_received' => [
            'sms' => [
                'enabled' => false,
                'message' => 'You have a new like from :liker_name',
            ],
            'database' => [
                'enabled' => false,
                'message' => 'You have a new like from :liker_name',
            ],
            'email' => [
                'enabled' => true,
                'subject' => 'New Like',
                'message' => 'You have a new like from :liker_name',
            ],
            'push' => [
                'enabled' => false,
                'message' => 'You have a new like from :liker_name',
            ],
        ],
        'report_received' => [
            'sms' => [
                'enabled' => false,
                'message' => 'You have a new report from :reporter_name',
            ],
            'database' => [
                'enabled' => false,
                'message' => 'You have a new report from :reporter_name',
            ],
            'email' => [
                'enabled' => true,
                'subject' => 'New Report',
                'message' => 'You have a new report from :reporter_name',
            ],
            'push' => [
                'enabled' => false,
                'message' => 'You have a new report from :reporter_name',
            ],
        ],
        'report_resolved' => [
            'sms' => [
                'enabled' => false,
                'message' => 'Your report has been resolved',
            ],
            'database' => [
                'enabled' => false,
                'message' => 'Your report has been resolved',
            ],
            'email' => [
                'enabled' => true,
                'subject' => 'Report Resolved',
                'message' => 'Your report has been resolved',
            ],
            'push' => [
                'enabled' => false,
                'message' => 'Your report has been resolved',
            ],
        ],
        'thread_created' => [
            'sms' => [
                'enabled' => false,
                'message' => 'You have a new thread from :creator_name',
            ],
            'database' => [
                'enabled' => false,
                'message' => 'You have a new thread from :creator_name',
            ],
            'email' => [
                'enabled' => true,
                'subject' => 'New Thread',
                'message' => 'You have a new thread from :creator_name',
            ],
            'push' => [
                'enabled' => false,
                'message' => 'You have a new thread from :creator_name',
            ],
        ],
        'thread_locked' => [
            'sms' => [
                'enabled' => false,
                'message' => 'Your thread has been locked',
            ],
            'database' => [
                'enabled' => false,
                'message' => 'Your thread has been locked',
            ],
            'email' => [
                'enabled' => true,
                'subject' => 'Thread Locked',
                'message' => 'Your thread has been locked',
            ],
            'push' => [
                'enabled' => false,
                'message' => 'Your thread has been locked',
            ],
        ],
        'thread_unlocked' => [
            'sms' => [
                'enabled' => false,
                'message' => 'Your thread has been unlocked',
            ],
            'database' => [
                'enabled' => false,
                'message' => 'Your thread has been unlocked',
            ],
            'email' => [
                'enabled' => true,
                'subject' => 'Thread Unlocked',
                'message' => 'Your thread has been unlocked',
            ],
            'push' => [
                'enabled' => false,
                'message' => 'Your thread has been unlocked',
            ],
        ],
    ],
];
