<?php

use Mailery\Sender\Domain\Repository\DomainRepository;
use Psr\Container\ContainerInterface;
use Cycle\ORM\ORMInterface;
use SPFLib\DNS\StandardResolver;
use Mailery\Sender\Domain\Entity\Domain;
use Mailery\Sender\Domain\Generator\SpfGenerator;
use Mailery\Sender\Domain\Generator\DkimGenerator;
use Mailery\Sender\Domain\Model\DnsCheckerList;
use Yiisoft\Factory\Definitions\Reference;

return [
    SpfGenerator::class => [
        '__class' => SpfGenerator::class,
        '__construct()' => [
            'include' => $params['maileryio/mailery-sender-domain']['spf-include'],
            'dnsResolver' => Reference::to(StandardResolver::class),
        ],
    ],

    DkimGenerator::class => [
        '__class' => DkimGenerator::class,
        '__construct()' => [
            'selector' => $params['maileryio/mailery-sender-domain']['dkim-selector'],
        ],
    ],

    DnsCheckerList::class => static function () use($params) {
        return new DnsCheckerList([
            new SpfChecker($params['maileryio/mailery-sender-domain']['spf-include']),
            new DkimChecker($params['maileryio/mailery-sender-domain']['dkim-selector']),
        ]);
    },

    DomainRepository::class => static function (ContainerInterface $container) {
        return $container
            ->get(ORMInterface::class)
            ->getRepository(Domain::class);
    },
];
