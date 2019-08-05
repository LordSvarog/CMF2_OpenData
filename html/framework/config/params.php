<?php

return [
    'HTMLPurifier' => [
        'Attr.AllowedFrameTargets' => [
            '_blank',
            '_self',
            '_parent',
            '_top',
        ],
        'HTML.Trusted' => true,
        'Filter.YouTube' => true,
    ],
    'sidebar' => [
        [
            'label' => 'Profile',
            'url' => ['/auth/profile'],
        ],
        [
            'label' => 'Exit',
            'url' => ['/auth/default/logout'],
        ],
    ],
    'menu' => [
        [
            'label' => 'Material',
            'icon' => 'ti-files',
            'items' => [
                [
                    'label' => 'Content',
                    'url' => ['/content/default'],
                ],
            ],
        ],
    ],
    'dropdown' => [
        [
            'label' => 'Users',
            'icon' => 'ti-user',
            'items' => [
                [
                    'label' => 'Auth',
                    'url' => ['/auth/auth'],
                ],
                [
                    'label' => 'Log',
                    'url' => ['/auth/log'],
                ],
            ],
        ],
        [
            'label' => 'System',
            'icon' => 'ti-settings',
            'items' => [
                [
                    'label' => 'Configure',
                    'url' => ['/configure'],
                ],
                [
                    'label' => 'Clear cache',
                    'url' => ['/system/default/flush-cache'],
                ],
                [
                    'label' => 'Backup',
                    'url' => ['/backup/default'],
                ],
            ],
        ],
    ],
];
