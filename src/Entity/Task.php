<?php

namespace GrowthTaskBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use GrowthTaskBundle\Enum\TaskLimitType;
use GrowthTaskBundle\Repository\TaskRepository;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\Ignore;
use Tourze\Arrayable\AdminArrayInterface;
use Tourze\Arrayable\ApiArrayInterface;
use Tourze\Arrayable\PlainArrayInterface;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineIpBundle\Attribute\CreateIpColumn;
use Tourze\DoctrineIpBundle\Attribute\UpdateIpColumn;
use Tourze\DoctrineSnowflakeBundle\Service\SnowflakeIdGenerator;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Attribute\CreatedByColumn;
use Tourze\DoctrineUserBundle\Attribute\UpdatedByColumn;
use Tourze\EasyAdmin\Attribute\Action\Creatable;
use Tourze\EasyAdmin\Attribute\Action\CurdAction;
use Tourze\EasyAdmin\Attribute\Action\Deletable;
use Tourze\EasyAdmin\Attribute\Action\Editable;
use Tourze\EasyAdmin\Attribute\Column\BoolColumn;
use Tourze\EasyAdmin\Attribute\Column\ExportColumn;
use Tourze\EasyAdmin\Attribute\Column\ListColumn;
use Tourze\EasyAdmin\Attribute\Column\PictureColumn;
use Tourze\EasyAdmin\Attribute\Field\FormField;
use Tourze\EasyAdmin\Attribute\Field\ImagePickerField;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '任务配置')]
#[Deletable]
#[Editable]
#[Creatable]
#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Table(name: 'growth_task_task', options: ['comment' => '任务配置'])]
class Task implements \Stringable, PlainArrayInterface, ApiArrayInterface, AdminArrayInterface
{
    use TimestampableAware;

    /**
     * order值大的排序靠前。有效的值范围是[0, 2^32].
     */
    #[IndexColumn]
    #[FormField]
    #[ListColumn(order: 95, sorter: true)]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['default' => '0', 'comment' => '次序值'])]
    private ?int $sortNumber = 0;

    public function getSortNumber(): ?int
    {
        return $this->sortNumber;
    }

    public function setSortNumber(?int $sortNumber): self
    {
        $this->sortNumber = $sortNumber;

        return $this;
    }

    public function retrieveSortableArray(): array
    {
        return [
            'sortNumber' => $this->getSortNumber(),
        ];
    }

    #[CreatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '创建人'])]
    private ?string $createdBy = null;

    #[UpdatedByColumn]
    #[ORM\Column(nullable: true, options: ['comment' => '更新人'])]
    private ?string $updatedBy = null;

    #[ExportColumn]
    #[ListColumn(order: -1, sorter: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(SnowflakeIdGenerator::class)]
    #[ORM\Column(type: Types::BIGINT, nullable: false, options: ['comment' => 'ID'])]
    private ?string $id = null;

    #[BoolColumn]
    #[IndexColumn]
    #[TrackColumn]
    #[ORM\Column(type: Types::BOOLEAN, nullable: true, options: ['comment' => '有效', 'default' => 0])]
    #[ListColumn(order: 97)]
    #[FormField(order: 97)]
    private ?bool $valid = false;

    #[FormField(title: '分组')]
    #[ListColumn(title: '分组')]
    #[ORM\ManyToOne(targetEntity: Grouping::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Grouping $grouping = null;

    #[FormField]
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '标题'])]
    private ?string $title = null;

    #[FormField]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    private ?string $description = null;

    #[FormField]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '奖励描述'])]
    private ?string $awardDescription = null;

    /**
     * @var Collection<Award>
     */
    #[Groups(['restful_read'])]
    #[ListColumn(title: '奖品')]
    #[CurdAction(label: '奖品', drawerWidth: 1200)]
    #[ORM\OneToMany(mappedBy: 'task', targetEntity: Award::class, orphanRemoval: true)]
    private Collection $awards;

    #[FormField]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '当前进度'])]
    private ?string $currentProgress = null;

    #[FormField]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '总进度'])]
    private ?string $totalProgress = null;

    #[ListColumn]
    #[FormField(span: 10)]
    #[Groups(['restful_read'])]
    #[ORM\Column(length: 20, nullable: true, enumType: TaskLimitType::class, options: ['comment' => '限制维度', 'default' => 'day'])]
    private ?TaskLimitType $limitType = null;

    #[FormField(span: 8)]
    #[ListColumn]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '限制次数'])]
    private ?int $limitTimes = null;

    #[Groups(['restful_read'])]
    #[FormField]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '限制开始时间'])]
    private ?\DateTimeInterface $startTime = null;

    #[Groups(['restful_read'])]
    #[FormField]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true, options: ['comment' => '限制结束时间'])]
    private ?\DateTimeInterface $endTime = null;

    #[FormField(title: '任务类型')]
    #[ListColumn(title: '任务类型')]
    #[ORM\ManyToOne(targetEntity: TaskType::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?TaskType $type = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '跳转路径'])]
    #[FormField]
    #[Groups(['restful_read'])]
    private ?string $redirectUrl = null;

    #[ImagePickerField]
    #[PictureColumn]
    #[FormField]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => 'logo'])]
    private ?string $logo = '';

    #[ImagePickerField]
    #[PictureColumn]
    #[FormField]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '进行中按钮'])]
    private ?string $buttonDoing = '';

    #[ImagePickerField]
    #[PictureColumn]
    #[FormField]
    #[Groups(['restful_read'])]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '已完成按钮'])]
    private ?string $buttonFinished = '';

    /**
     * @var Collection<Record>
     */
    #[Ignore]
    #[ORM\OneToMany(mappedBy: 'task', targetEntity: Record::class)]
    private Collection $records;

    #[Groups(['restful_read'])]
    #[FormField]
    #[ORM\Column(length: 100, nullable: true, options: ['comment' => 'Tracking'])]
    private ?string $tracking = null;

    /**
     * @var Collection<TaskAttribute>
     */
    #[ListColumn(title: '属性')]
    #[CurdAction(label: '属性', drawerWidth: 1200)]
    #[ORM\OneToMany(mappedBy: 'task', targetEntity: TaskAttribute::class)]
    private Collection $taskAttributes;

    #[CreateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '创建时IP'])]
    private ?string $createdFromIp = null;

    #[UpdateIpColumn]
    #[ORM\Column(length: 128, nullable: true, options: ['comment' => '更新时IP'])]
    private ?string $updatedFromIp = null;

    public function __construct()
    {
        $this->awards = new ArrayCollection();
        $this->records = new ArrayCollection();
        $this->taskAttributes = new ArrayCollection();
    }

    public function __toString(): string
    {
        if (!$this->getId()) {
            return '';
        }

        return "{$this->getId()}:{$this->getTitle()}";
    }

    public function setCreatedBy(?string $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): self
    {
        $this->valid = $valid;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
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

    public function getGrouping(): ?Grouping
    {
        return $this->grouping;
    }

    public function setGrouping(?Grouping $grouping): self
    {
        $this->grouping = $grouping;

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

    /**
     * @return Collection<int, Award>
     */
    public function getAwards(): Collection
    {
        return $this->awards;
    }

    public function addAward(Award $reward): self
    {
        if (!$this->awards->contains($reward)) {
            $this->awards[] = $reward;
            $reward->setTask($this);
        }

        return $this;
    }

    public function removeAward(Award $reward): self
    {
        if ($this->awards->removeElement($reward)) {
            // set the owning side to null (unless already changed)
            if ($reward->getTask() === $this) {
                $reward->setTask(null);
            }
        }

        return $this;
    }

    public function getCurrentProgress(): ?string
    {
        return $this->currentProgress;
    }

    public function setCurrentProgress(?string $currentProgress): self
    {
        $this->currentProgress = $currentProgress;

        return $this;
    }

    public function getTotalProgress(): ?string
    {
        return $this->totalProgress;
    }

    public function setTotalProgress(?string $totalProgress): self
    {
        $this->totalProgress = $totalProgress;

        return $this;
    }

    public function getButtonDoing(): ?string
    {
        return $this->buttonDoing;
    }

    public function setButtonDoing(?string $buttonDoing): self
    {
        $this->buttonDoing = $buttonDoing;

        return $this;
    }

    public function getButtonFinished(): ?string
    {
        return $this->buttonFinished;
    }

    public function setButtonFinished(?string $buttonFinished): self
    {
        $this->buttonFinished = $buttonFinished;

        return $this;
    }

    /**
     * @return Collection<int, Record>
     */
    public function getRecords(): Collection
    {
        return $this->records;
    }

    public function addRecord(Record $record): self
    {
        if (!$this->records->contains($record)) {
            $this->records[] = $record;
            $record->setTask($this);
        }

        return $this;
    }

    public function removeRecord(Record $record): self
    {
        if ($this->records->removeElement($record)) {
            // set the owning side to null (unless already changed)
            if ($record->getTask() === $this) {
                $record->setTask(null);
            }
        }

        return $this;
    }

    public function getLimitTimes(): ?int
    {
        return $this->limitTimes;
    }

    public function setLimitTimes(int $limitTimes): self
    {
        $this->limitTimes = $limitTimes;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getType(): ?TaskType
    {
        return $this->type;
    }

    public function setType(TaskType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    public function setRedirectUrl(?string $redirectUrl): self
    {
        $this->redirectUrl = $redirectUrl;

        return $this;
    }

    public function getAwardDescription(): ?string
    {
        return $this->awardDescription;
    }

    public function setAwardDescription(?string $awardDescription): self
    {
        $this->awardDescription = $awardDescription;

        return $this;
    }

    public function getTracking(): ?string
    {
        return $this->tracking;
    }

    public function setTracking(?string $tracking): self
    {
        $this->tracking = $tracking;

        return $this;
    }

    /**
     * @return Collection<int, TaskAttribute>
     */
    public function getTaskAttributes(): Collection
    {
        return $this->taskAttributes;
    }

    public function addTaskAttribute(TaskAttribute $taskAttribute): self
    {
        if (!$this->taskAttributes->contains($taskAttribute)) {
            $this->taskAttributes->add($taskAttribute);
            $taskAttribute->setTask($this);
        }

        return $this;
    }

    public function removeTaskAttribute(TaskAttribute $taskAttribute): self
    {
        if ($this->taskAttributes->removeElement($taskAttribute)) {
            // set the owning side to null (unless already changed)
            if ($taskAttribute->getTask() === $this) {
                $taskAttribute->setTask(null);
            }
        }

        return $this;
    }

    public function getLimitType(): ?TaskLimitType
    {
        return $this->limitType;
    }

    public function setLimitType(?TaskLimitType $limitType): self
    {
        $this->limitType = $limitType;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(?\DateTimeInterface $startTime): self
    {
        $this->startTime = $startTime;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(?\DateTimeInterface $endTime): self
    {
        $this->endTime = $endTime;

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

    public function retrievePlainArray(): array
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'awardDescription' => $this->getAwardDescription(),
            'limitType' => $this->getLimitType()?->value,
            'limitTimes' => $this->getLimitTimes(),
            'startTime' => $this->getStartTime(),
            'endTime' => $this->getEndTime(),
            'redirectUrl' => $this->getRedirectUrl(),
            'logo' => $this->getLogo(),
            'buttonDoing' => $this->getButtonDoing(),
            'buttonFinished' => $this->getButtonFinished(),
            'tracking' => $this->getTracking(),
            'currentProgress' => $this->getCurrentProgress(),
            'totalProgress' => $this->getTotalProgress(),
            'valid' => $this->isValid(),
            ...$this->retrieveSortableArray(),
            'createTime' => $this->getCreateTime()?->format('Y-m-d H:i:s'),
            'updateTime' => $this->getUpdateTime()?->format('Y-m-d H:i:s'),
        ];
    }

    public function retrieveApiArray(): array
    {
        $result = $this->retrievePlainArray();
        $result['awards'] = [];
        foreach ($this->getAwards() as $award) {
            $result['awards'][] = $award->retrievePlainArray();
        }
        $result['type'] = [
            'id' => $this->getType()?->getId(),
            'code' => $this->getType()?->getCode(),
            'title' => $this->getType()?->getTitle(),
        ];

        return $result;
    }

    public function retrieveAdminArray(): array
    {
        return $this->retrievePlainArray();
    }
}
