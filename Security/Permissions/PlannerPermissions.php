<?php

declare(strict_types=1);

namespace MauticPlugin\MauticMarketingPlannerBundle\Security\Permissions;

use Mautic\CoreBundle\Security\Permissions\AbstractPermissions;
use Symfony\Component\Form\FormBuilderInterface;

class PlannerPermissions extends AbstractPermissions
{
    public function __construct(array $params)
    {
        parent::__construct($params);
        $this->addStandardPermissions('items');
    }

    public function getName(): string
    {
        return 'plugin:marketingplanner';
    }

    public function buildForm(FormBuilderInterface &$builder, array $options, array $data): void
    {
        $this->addStandardFormFields('plugin:marketingplanner', 'items', $builder, $data);
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }
}
