<?php

namespace GrowthTaskBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use GrowthTaskBundle\Repository\TaskAttributeRepository;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: TaskAttributeRepository::class)]
#[ORM\Table(name: 'growth_task_task_attribute', options: ['comment' => '任务属性'])]
#[ORM\UniqueConstraint(name: 'idx_uniq_task_name', columns: ['task_id', 'name'])]
class TaskAttribute implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private ?int $id = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\Column(length: 100, options: ['comment' => '名称'])]
    private ?string $name = null;

    #[ORM\Column(length: 255, options: ['comment' => '内容'])]
    private ?string $value = null;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'taskAttributes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Task $task = null;

    public function __toString()
    {
        if ($this->getId() === null || $this->getId() === 0) {
            return '';
        }

        return "{$this->getName()}:{$this->getValue()}";
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $task): self
    {
        $this->task = $task;

        return $this;
    }
}
