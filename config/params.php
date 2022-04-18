<?php

use Mailery\Sender\Domain\Model\DomainDkimBucket;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Definitions\Reference;
use Mailery\Sender\Domain\Entity\Dkim;
use Mailery\Sender\Domain\Entity\DnsRecord;
use Mailery\Sender\Domain\Entity\Domain;
return [
    'yiisoft/yii-cycle' => [
        'entity-paths' => [
            '@vendor/maileryio/mailery-sender-domain/src/Entity',
        ],
    ],

    'maileryio/mailery-activity-log' => [
        'entity-groups' => [
            'sender' => [
                'entities' => [
                    Dkim::class,
                    DnsRecord::class,
                    Domain::class,
                ],
            ],
        ],
    ],

    'maileryio/mailery-storage' => [
        'buckets' => [
            Reference::to(DomainDkimBucket::class),
        ],
    ],

    'maileryio/mailery-sender-domain' => [
        'spf-domain-spec' => '_spf.mailery.io',
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
