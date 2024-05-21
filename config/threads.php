<?php

declare(strict_types=1);

return [
    'route_prefix' => 'api',
    'middleware' => ['api'],
    'user_model' => 'App\Models\User',
    'user_name_field' => 'display_name',
    'user_avatar_field' => 'avatar',
    'thread_show_url' => 'http://localhost:8000/thread',
    'threaded_user_relations' => [
        [
            'fundraiser',
        ],
        [
            'cause',
            'business',
            'user',
        ],
        [
            'cause',
            'user',
        ],
    ],

    'notifications' => [
        'comment_created' => [
            'name_field' => 'commenter_name',
            'sms' => [
                'enabled' => false,
                'message' => 'You have a new comment from :commenter_name',
            ],
            'database' => [
                'enabled' => true,
                'message' => 'You have a new comment from :commenter_name',
            ],
            'email' => [
                'enabled' => true,
                'subject' => 'New Comment',
                'message' => 'You have a new comment from :commenter_name',
            ],
            'push' => [
                'enabled' => true,
                'message' => 'You have a new comment from :commenter_name',
            ],
        ],
        'commenter_blocked' => [
            'name_field' => 'blocker_name',
            'sms' => [
                'enabled' => false,
                'message' => 'You have been blocked by :blocker_name',
            ],
            'database' => [
                'enabled' => true,
                'message' => 'You have been blocked by :blocker_name',
            ],
            'email' => [
                'enabled' => true,
                'subject' => 'You have been blocked from starting threads and commenting',
                'message' => 'You have been blocked by :blocker_name',
            ],
            'push' => [
                'enabled' => true,
                'message' => 'You have been blocked by :blocker_name',
            ],
        ],
        'commenter_unblocked' => [
            'name_field' => 'unblocker_name',
            'sms' => [
                'enabled' => false,
                'message' => 'You have been unblocked by :unblocker_name',
            ],
            'database' => [
                'enabled' => true,
                'message' => 'You have been unblocked by :unblocker_name',
            ],
            'email' => [
                'enabled' => true,
                'subject' => 'You have been unblocked from starting threads and commenting',
                'message' => 'You have been unblocked by :unblocker_name',
            ],
            'push' => [
                'enabled' => true,
                'message' => 'You have been unblocked by :unblocker_name',
            ],
        ],
        'like_received' => [
            'name_field' => 'liker_name',
            'sms' => [
                'enabled' => false,
                'message' => 'You have a new like from :liker_name',
            ],
            'database' => [
                'enabled' => true,
                'message' => 'You have a new like from :liker_name',
            ],
            'email' => [
                'enabled' => true,
                'subject' => 'New Like',
                'message' => 'You have a new like from :liker_name',
            ],
            'push' => [
                'enabled' => true,
                'message' => 'You have a new like from :liker_name',
            ],
        ],
        'report_received' => [
            'name_field' => 'type',
            'sms' => [
                'enabled' => false,
                'message' => 'You have a received a :type report',
            ],
            'database' => [
                'enabled' => false,
                'message' => 'You have a received a :type report',
            ],
            'email' => [
                'enabled' => false,
                'subject' => 'New Report',
                'message' => 'You have a received a :type report',
            ],
            'push' => [
                'enabled' => false,
                'message' => 'You have a received a :type report',
            ],
        ],
        'report_resolved' => [
            'name_field' => 'type',
            'sms' => [
                'enabled' => false,
                'message' => 'Your :type report has been resolved',
            ],
            'database' => [
                'enabled' => false,
                'message' => 'Your :type report has been resolved',
            ],
            'email' => [
                'enabled' => false,
                'subject' => 'Report Resolved',
                'message' => 'Your :type report has been resolved',
            ],
            'push' => [
                'enabled' => false,
                'message' => 'Your :type report has been resolved',
            ],
        ],
        'thread_created' => [
            'name_field' => 'creator_name',
            'sms' => [
                'enabled' => false,
                'message' => 'You have a new thread from :creator_name',
            ],
            'database' => [
                'enabled' => true,
                'message' => 'You have a new thread from :creator_name',
            ],
            'email' => [
                'enabled' => true,
                'subject' => 'New Thread',
                'message' => 'You have a new thread from :creator_name',
            ],
            'push' => [
                'enabled' => true,
                'message' => 'You have a new thread from :creator_name',
            ],
        ],
        'thread_locked' => [
            'sms' => [
                'enabled' => false,
                'message' => 'Your thread has been locked',
            ],
            'database' => [
                'enabled' => true,
                'message' => 'Your thread has been locked',
            ],
            'email' => [
                'enabled' => true,
                'subject' => 'Thread Locked',
                'message' => 'Your thread has been locked',
            ],
            'push' => [
                'enabled' => true,
                'message' => 'Your thread has been locked',
            ],
        ],
        'thread_unlocked' => [
            'sms' => [
                'enabled' => false,
                'message' => 'Your thread has been unlocked',
            ],
            'database' => [
                'enabled' => true,
                'message' => 'Your thread has been unlocked',
            ],
            'email' => [
                'enabled' => true,
                'subject' => 'Thread Unlocked',
                'message' => 'Your thread has been unlocked',
            ],
            'push' => [
                'enabled' => true,
                'message' => 'Your thread has been unlocked',
            ],
        ],
    ],

    'listeners' => [
        'comment_created' => true,
        'commenter_blocked' => true,
        'commenter_unblocked' => true,
        'like_received' => true,
        'report_received' => true,
        'report_resolved' => true,
        'thread_created' => true,
        'thread_locked' => true,
        'thread_unlocked' => true,
    ],
];
