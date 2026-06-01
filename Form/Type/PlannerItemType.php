<?php

declare(strict_types=1);

namespace MauticPlugin\MauticMarketingPlannerBundle\Form\Type;

use Mautic\CoreBundle\Form\Type\FormButtonsType;
use Mautic\UserBundle\Entity\User;
use MauticPlugin\MauticMarketingPlannerBundle\Entity\PlannerItem;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlannerItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label'    => 'mautic.marketing_planner.item.name',
                'required' => true,
                'attr'     => ['class' => 'form-control'],
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'mautic.marketing_planner.item.description',
                'required' => false,
                'attr'     => ['class' => 'form-control', 'rows' => 5],
            ])
            ->add('deadline', DateType::class, [
                'label'  => 'mautic.marketing_planner.item.deadline',
                'widget' => 'single_text',
                'attr'   => ['class' => 'form-control'],
            ])
            ->add('doneAt', DateType::class, [
                'label'    => 'mautic.marketing_planner.item.done_at',
                'widget'   => 'single_text',
                'required' => false,
                'attr'     => ['class' => 'form-control'],
            ])
            ->add('assignedTo', EntityType::class, [
                'label'        => 'mautic.marketing_planner.item.assigned_to',
                'class'        => User::class,
                'choice_label' => function (User $user): string {
                    return trim($user->getFirstName().' '.$user->getLastName()) ?: $user->getUsername();
                },
                'required'    => false,
                'placeholder' => 'mautic.marketing_planner.unassigned',
                'attr'        => ['class' => 'form-control'],
            ])
            ->add('buttons', FormButtonsType::class, [
                'apply_text'  => false,
                'cancel_attr' => ['href' => 'javascript:history.go(-1)'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PlannerItem::class,
        ]);
    }
}
