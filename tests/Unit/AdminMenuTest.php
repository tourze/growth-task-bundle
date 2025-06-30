<?php

namespace GrowthTaskBundle\Tests\Unit;

use GrowthTaskBundle\AdminMenu;
use GrowthTaskBundle\Entity\Grouping;
use GrowthTaskBundle\Entity\Record;
use GrowthTaskBundle\Entity\Task;
use GrowthTaskBundle\Entity\TaskType;
use Knp\Menu\ItemInterface;
use PHPUnit\Framework\TestCase;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;

class AdminMenuTest extends TestCase
{
    private AdminMenu $adminMenu;
    private LinkGeneratorInterface $linkGenerator;
    private ItemInterface $item;

    protected function setUp(): void
    {
        $this->linkGenerator = $this->createMock(LinkGeneratorInterface::class);
        $this->adminMenu = new AdminMenu($this->linkGenerator);
        $this->item = $this->createMock(ItemInterface::class);
    }

    public function testInvoke(): void
    {
        $this->linkGenerator->expects($this->exactly(4))
            ->method('getCurdListPage')
            ->willReturnMap([
                [TaskType::class, '/admin/task-type'],
                [Grouping::class, '/admin/grouping'],
                [Task::class, '/admin/task'],
                [Record::class, '/admin/record']
            ]);

        $rootItem = $this->createMock(ItemInterface::class);
        $taskTypeItem = $this->createMock(ItemInterface::class);
        $groupingItem = $this->createMock(ItemInterface::class);
        $taskItem = $this->createMock(ItemInterface::class);
        $recordItem = $this->createMock(ItemInterface::class);

        $this->item->expects($this->once())
            ->method('addChild')
            ->with('成长任务')
            ->willReturn($rootItem);

        $this->item->expects($this->exactly(4))
            ->method('getChild')
            ->with('成长任务')
            ->willReturn($rootItem);

        $rootItem->expects($this->exactly(4))
            ->method('addChild')
            ->willReturnMap([
                ['任务类型', $taskTypeItem],
                ['分组管理', $groupingItem], 
                ['任务配置', $taskItem],
                ['完成记录', $recordItem]
            ]);

        $taskTypeItem->expects($this->once())
            ->method('setUri')
            ->with('/admin/task-type')
            ->willReturnSelf();

        $groupingItem->expects($this->once())
            ->method('setUri')
            ->with('/admin/grouping')
            ->willReturnSelf();

        $taskItem->expects($this->once())
            ->method('setUri')
            ->with('/admin/task')
            ->willReturnSelf();

        $recordItem->expects($this->once())
            ->method('setUri')
            ->with('/admin/record')
            ->willReturnSelf();

        ($this->adminMenu)($this->item);
    }
}