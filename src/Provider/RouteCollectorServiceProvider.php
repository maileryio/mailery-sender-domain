<?php

namespace Mailery\Sender\Domain\Provider;

use Yiisoft\Di\Container;
use Yiisoft\Di\Support\ServiceProvider;
use Yiisoft\Router\RouteCollectorInterface;
use Yiisoft\Router\Group;
use Yiisoft\Router\Route;
use Mailery\Sender\Domain\Controller\SettingsController;

final class RouteCollectorServiceProvider extends ServiceProvider
{
    /**
     * @param Container $container
     * @return void
     */
    public function register(Container $container): void
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
