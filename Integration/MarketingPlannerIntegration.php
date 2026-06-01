<?php

declare(strict_types=1);

namespace MauticPlugin\MauticMarketingPlannerBundle\Integration;

use Mautic\PluginBundle\Integration\AbstractIntegration;

class MarketingPlannerIntegration extends AbstractIntegration
{
    public function getName(): string
    {
        return 'MarketingPlanner';
    }

    public function getDisplayName(): string
    {
        return 'Marketing Planner';
    }

    public function getAuthenticationType(): string
    {
        return 'none';
    }

    public function getRequiredKeyFields(): array
    {
        return [];
    }
}
