<?php

declare(strict_types=1);

namespace MauticPlugin\MauticMarketingPlannerBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Mautic\CoreBundle\Doctrine\Mapping\ClassMetadataBuilder;
use Mautic\UserBundle\Entity\User;

class PlannerItem
{
    private ?int $id = null;
    private string $name = '';
    private ?string $description = null;
    private \DateTimeInterface $createdAt;
    private \DateTimeInterface $deadline;
    private ?\DateTimeInterface $doneAt = null;
    private ?User $assignedTo = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->deadline  = new \DateTime();
    }

    public static function loadMetadata(ORM\ClassMetadata $metadata): void
    {
        $builder = new ClassMetadataBuilder($metadata);

        $builder->setTable('planner_items')
            ->setCustomRepositoryClass(PlannerItemRepository::class)
            ->addId()
            ->addNamedField('name', Types::STRING, 'name')
            ->addNamedField('description', Types::TEXT, 'description', true)
            ->addNamedField('createdAt', Types::DATETIME_MUTABLE, 'created_at')
            ->addNamedField('deadline', Types::DATE_MUTABLE, 'deadline')
            ->addNamedField('doneAt', Types::DATE_MUTABLE, 'done_at', true);

        $builder->createManyToOne('assignedTo', User::class)
            ->addJoinColumn('assigned_to_id', 'id', true, false, 'SET NULL')
            ->build();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getDeadline(): \DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(\DateTimeInterface $deadline): self
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getDoneAt(): ?\DateTimeInterface
    {
        return $this->doneAt;
    }

    public function setDoneAt(?\DateTimeInterface $doneAt): self
    {
        $this->doneAt = $doneAt;

        return $this;
    }

    public function isDone(): bool
    {
        return $this->doneAt !== null;
    }

    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?User $assignedTo): self
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }
}
