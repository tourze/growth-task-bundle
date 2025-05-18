<?php

namespace GrowthTaskBundle;

use GrowthTaskBundle\Entity\Grouping;
use GrowthTaskBundle\Entity\Record;
use GrowthTaskBundle\Entity\Task;
use GrowthTaskBundle\Entity\TaskType;
use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;

class AdminMenu implements MenuProviderInterface
{
    public function __construct(private readonly LinkGeneratorInterface $linkGenerator)
    {
    }

    public function __invoke(ItemInterface $item): void
    {
        $item->addChild('成长任务');
        $item->getChild('成长任务')->addChild('任务类型')->setUri($this->linkGenerator->getCurdListPage(TaskType::class));
        $item->getChild('成长任务')->addChild('分组管理')->setUri($this->linkGenerator->getCurdListPage(Grouping::class));
        $item->getChild('成长任务')->addChild('任务配置')->setUri($this->linkGenerator->getCurdListPage(Task::class));
        $item->getChild('成长任务')->addChild('完成记录')->setUri($this->linkGenerator->getCurdListPage(Record::class));
    }
}
