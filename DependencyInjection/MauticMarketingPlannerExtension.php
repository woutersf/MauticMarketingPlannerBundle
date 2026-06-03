<?php

declare(strict_types=1);

namespace MauticPlugin\MauticMarketingPlannerBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

class MauticMarketingPlannerExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('doctrine_migrations', [
            'migrations_paths' => [
                'MauticPlugin\MauticMarketingPlannerBundle\Migration' => __DIR__.'/../Migration',
            ],
        ]);
    }
}
