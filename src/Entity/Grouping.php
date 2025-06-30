<?php

namespace GrowthTaskBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use GrowthTaskBundle\Repository\GroupingRepository;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: GroupingRepository::class)]
#[ORM\Table(name: 'growth_task_grouping', options: ['comment' => '任务分组'])]
class Grouping implements \Stringable
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

    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => '标题'])]
    private ?string $title = null;

    /**
     * @var Collection<Task>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'grouping', targetEntity: Task::class)]
    private Collection $tasks;

    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function __toString(): string
    {
        if ($this->id === null || $this->id === 0) {
            return '';
        }

        return $this->getTitle() ?? '';
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $config): self
    {
        if (!$this->tasks->contains($config)) {
            $this->tasks[] = $config;
            $config->setGrouping($this);
        }

        return $this;
    }

    public function removeTask(Task $config): self
    {
        if ($this->tasks->removeElement($config)) {
            // set the owning side to null (unless already changed)
            if ($config->getGrouping() === $this) {
                $config->setGrouping(null);
            }
        }

        return $this;
    }

    public function setCreatedFromIp(?string $createdFromIp): self
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): self
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }
}
