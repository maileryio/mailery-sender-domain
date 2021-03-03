<?php

use Yiisoft\Router\UrlGeneratorInterface;

return [
    'yiisoft/yii-cycle' => [
        'annotated-entity-paths' => [
            '@vendor/maileryio/mailery-sender-domain/src/Entity',
        ],
    ],

    'maileryio/mailery-sender-domain' => [
        'spf-include' => '_spf.mailery.io',
        'dkim-selector' => 'mailery',
    ],

    'maileryio/mailery-menu-sidebar' => [
        'items' => [
            'settings' => [
                'activeRouteNames' => [
                    '/brand/settings/domain',
                ],
            ],
        ],
    ],

    'maileryio/mailery-brand' => [
        'settings-menu' => [
            'items' => [
                'domain' => [
                    'label' => static function () {
                        return 'Domain verification';
                    },
                    'url' => static function (UrlGeneratorInterface $urlGenerator) {
                        return $urlGenerator->generate('/brand/settings/domain');
                    },
                ],
            ],
        ],
    ],
];
