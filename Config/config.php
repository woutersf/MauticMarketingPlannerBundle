<?php

declare(strict_types=1);

return [
    'name'        => 'Marketing Planner',
    'description' => 'A shared marketing calendar to plan, track and manage team marketing activities.',
    'version'     => '1.0.0',
    'author'      => 'Dropsolid',

    'routes' => [
        'main' => [
            'mautic_marketing_planner_index' => [
                'path'       => '/planner',
                'controller' => 'MauticPlugin\MauticMarketingPlannerBundle\Controller\PlannerController::indexAction',
            ],
            'mautic_marketing_planner_new' => [
                'path'       => '/planner/new',
                'controller' => 'MauticPlugin\MauticMarketingPlannerBundle\Controller\PlannerController::newAction',
            ],
            'mautic_marketing_planner_edit' => [
                'path'       => '/planner/edit/{id}',
                'controller' => 'MauticPlugin\MauticMarketingPlannerBundle\Controller\PlannerController::editAction',
                'defaults'   => ['id' => 0],
            ],
            'mautic_marketing_planner_delete' => [
                'path'       => '/planner/delete/{id}',
                'controller' => 'MauticPlugin\MauticMarketingPlannerBundle\Controller\PlannerController::deleteAction',
                'methods'    => ['POST'],
            ],
            'mautic_marketing_planner_done' => [
                'path'       => '/planner/done/{id}',
                'controller' => 'MauticPlugin\MauticMarketingPlannerBundle\Controller\PlannerController::doneAction',
                'methods'    => ['POST'],
            ],
        ],
        'api' => [],
    ],

    'menu' => [
        'main' => [
            'mautic.marketing_planner.menu' => [
                'route'     => 'mautic_marketing_planner_index',
                'iconClass' => 'ri-calendar-line',
                'id'        => 'mautic_marketing_planner_index',
                'access'    => 'plugin:marketingplanner:items:viewother',
                'priority'  => -100,
            ],
        ],
    ],

    'services' => [
        'forms' => [
            'mautic.marketing_planner.form.type.item' => [
                'class'     => \MauticPlugin\MauticMarketingPlannerBundle\Form\Type\PlannerItemType::class,
                'arguments' => ['doctrine.orm.entity_manager'],
                'tags'      => ['form.type'],
            ],
        ],
        'permissions' => [
            'mautic.marketing_planner.permissions' => [
                'class'     => \MauticPlugin\MauticMarketingPlannerBundle\Security\Permissions\PlannerPermissions::class,
                'arguments' => ['%mautic.bundles%'],
            ],
        ],
        'integrations' => [
            'mautic.integration.marketing_planner' => [
                'class'     => \MauticPlugin\MauticMarketingPlannerBundle\Integration\MarketingPlannerIntegration::class,
                'arguments' => [
                    'event_dispatcher',
                    'mautic.helper.cache_storage',
                    'doctrine.orm.entity_manager',
                    'request_stack',
                    'router',
                    'translator',
                    'monolog.logger.mautic',
                    'mautic.helper.encryption',
                    'mautic.lead.model.lead',
                    'mautic.lead.model.company',
                    'mautic.helper.paths',
                    'mautic.core.model.notification',
                    'mautic.lead.model.field',
                    'mautic.plugin.model.integration_entity',
                    'mautic.lead.model.dnc',
                    'mautic.lead.field.fields_with_unique_identifier',
                ],
            ],
        ],
    ],
];
