<?php

namespace GrowthTaskBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use GrowthTaskBundle\Enum\AwardType;
use GrowthTaskBundle\Enum\RangeType;
use GrowthTaskBundle\Repository\AwardRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: AwardRepository::class)]
#[ORM\Table(name: 'growth_task_award', options: ['comment' => '任务奖品'])]
class Award implements \Stringable, PlainArrayInterface, AdminArrayInterface
{
    use TimestampableAware;
    use BlameableAware;
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '奖品ID'])]
    private ?int $id = 0;

    #[Ignore]
    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'awards')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Task $task = null;

    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 50, enumType: AwardType::class, options: ['comment' => '类型'])]
    private AwardType $type;

    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '名称'])]
    private ?string $name = null;

    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '奖项'])]
    private ?string $value = null;

    #[Groups(groups: ['restful_read'])]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '完成次数', 'default' => 1])]
    private ?int $times = null;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, enumType: RangeType::class, options: ['comment' => '次数范围', 'default' => 'equals'])]
    private ?RangeType $rangeType = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '备注'])]
    private ?string $remark = null;

    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => 1, 'comment' => '乐观锁版本号'])]
    private ?int $lockVersion = null;

    #[ORM\Column(length: 30, nullable: true, options: ['comment' => '奖品描述'])]
    private ?string $description = null;

    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    public function __toString(): string
    {
        if ($this->id === null || $this->id === 0) {
            return '';
        }

        return "{$this->getId()}: {$this->getName()} - {$this->getValue()}";
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTask(): ?Task
    {
        return $this->task;
    }

    public function setTask(?Task $config): self
    {
        $this->task = $config;

        return $this;
    }

    public function getType(): ?AwardType
    {
        return $this->type;
    }

    public function setType(AwardType $type): self
    {
        $this->type = $type;

        return $this;
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

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(?string $remark): self
    {
        $this->remark = $remark;

        return $this;
    }

    public function getTimes(): ?int
    {
        return $this->times;
    }

    public function setTimes(int $times): self
    {
        $this->times = $times;

        return $this;
    }

    public function getRangeType(): ?RangeType
    {
        return $this->rangeType;
    }

    public function setRangeType(?RangeType $rangeType): self
    {
        $this->rangeType = $rangeType;

        return $this;
    }

    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'type' => $this->getType()?->value,
            'name' => $this->getName(),
            'value' => $this->getValue(),
            'times' => $this->getTimes(),
            'description' => $this->getDescription(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function retrieveAdminArray(): array
    {
        return $this->retrievePlainArray();
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLockVersion(): ?int
    {
        return $this->lockVersion;
    }

    public function setLockVersion(?int $lockVersion): self
    {
        $this->lockVersion = $lockVersion;

        return $this;
    }

    public function getCreatedFromIp(): ?string
    {
        return $this->createdFromIp;
    }

    public function setCreatedFromIp(?string $createdFromIp): static
    {
        $this->createdFromIp = $createdFromIp;

        return $this;
    }

    public function getUpdatedFromIp(): ?string
    {
        return $this->updatedFromIp;
    }

    public function setUpdatedFromIp(?string $updatedFromIp): static
    {
        $this->updatedFromIp = $updatedFromIp;

        return $this;
    }}
