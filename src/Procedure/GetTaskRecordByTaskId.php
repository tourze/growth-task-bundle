<?php

namespace GrowthTaskBundle\Procedure;

use GrowthTaskBundle\Repository\RecordRepository;
use GrowthTaskBundle\Repository\TaskRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;

#[MethodExpose('GetTaskRecordByTaskId')]
#[MethodTag('任务模块')]
#[MethodDoc('根据任务ID获取任务的完成记录以及对应奖励')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
class GetTaskRecordByTaskId extends BaseProcedure
{
    #[MethodParam('任务ID')]
    public string $taskId;

    public function __construct(
        private readonly RecordRepository $recordRepository,
        private readonly Security $security,
        private readonly TaskRepository $taskRepository,
    ) {
    }

    public function execute(): array
    {
        $task = $this->taskRepository->find($this->taskId);
        if (empty($task)) {
            throw new ApiException('任务不存在');
        }

        $records = $this->recordRepository->findBy([
            'task' => $task,
            'user' => $this->security->getUser(),
        ]);

        $list = [];
        foreach ($records as $record) {
            $list[] = $record->retrievePlainArray();
        }

        return $list;
    }
}
