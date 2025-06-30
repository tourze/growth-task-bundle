<?php

namespace GrowthTaskBundle\Procedure;

use GrowthTaskBundle\Repository\TaskRepository;
use GrowthTaskBundle\Service\TaskService;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Exception\ApiException;
use Tourze\JsonRPCLockBundle\Procedure\LockableProcedure;
use Tourze\JsonRPCLogBundle\Attribute\Log;

#[MethodExpose(method: 'ReportTask')]
#[MethodTag(name: '任务模块')]
#[MethodDoc(summary: '任务完成上报接口')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
#[Log]
class ReportTask extends LockableProcedure
{
    #[MethodParam(description: '任务ID')]
    public string $taskId;

    public function __construct(
        private readonly TaskRepository $taskRepository,
        private readonly TaskService $taskService,
        private readonly Security $security,
    ) {
    }

    public function execute(): array
    {
        $task = $this->taskRepository->find($this->taskId);
        if (empty($task)) {
            throw new ApiException('请求失败');
        }

        $rewards = $this->taskService->saveTask($task, $this->security->getUser());
        $result['desc'] = $rewards['desc'];
        if (isset($rewards['record'])) {
            $result['record'] = $rewards['record']->retrievePlainArray();
        }
        $result['hasAward'] = false;

        if (!empty($result['desc'])) {
            $result['hasAward'] = true;
            $result['__showToast'] = $result['desc'];
        }

        return $result;
    }
}
