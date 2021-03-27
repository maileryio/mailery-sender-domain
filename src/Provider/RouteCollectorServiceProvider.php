<?php

namespace Mailery\Sender\Domain\Provider;

use Psr\Container\ContainerInterface;
use Yiisoft\Di\Support\ServiceProvider;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Mailery\Sender\Domain\Controller\SettingsController;

final class RouteCollectorServiceProvider extends ServiceProvider
{
    /**
     * @param ContainerInterface $container
     * @return void
     */
    public function register(ContainerInterface $container): void
    {
        /** @var RouteCollectorInterface $collector */
        $collector = $container->get(RouteCollectorInterface::class);

        $collector->addGroup(
            Group::create(
                '/brand/{brandId:\d+}',
                [
                    Route::methods(['GET', 'POST'], '/settings/domain', [SettingsController::class, 'domain'])
                        ->name('/brand/settings/domain'),
                    Route::methods(['POST'], '/settings/check-dns', [SettingsController::class, 'checkDns'])
                        ->name('/brand/settings/check-dns'),
                ]
            )
        );
    }
}
