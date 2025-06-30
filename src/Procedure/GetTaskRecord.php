<?php

namespace GrowthTaskBundle\Procedure;

use GrowthTaskBundle\Entity\Record;
use GrowthTaskBundle\Enum\AwardType;
use GrowthTaskBundle\Repository\RecordRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Tourze\JsonRPC\Core\Attribute\MethodDoc;
use Tourze\JsonRPC\Core\Attribute\MethodExpose;
use Tourze\JsonRPC\Core\Attribute\MethodParam;
use Tourze\JsonRPC\Core\Attribute\MethodTag;
use Tourze\JsonRPC\Core\Procedure\BaseProcedure;
use Tourze\JsonRPCPaginatorBundle\Procedure\PaginatorTrait;

#[MethodExpose(method: 'GetTaskRecord')]
#[MethodTag(name: '任务模块')]
#[MethodDoc(summary: '获取任务的完成记录以及对应奖励')]
#[IsGranted(attribute: 'IS_AUTHENTICATED_FULLY')]
class GetTaskRecord extends BaseProcedure
{
    use PaginatorTrait;

    #[MethodParam(description: '奖励类型')]
    public string $awardType = '';

    public function __construct(
        private readonly RecordRepository $recordRepository,
        private readonly Security $security,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    public function execute(): array
    {
        $qb = $this->recordRepository->createQueryBuilder('r')
            ->where('r.user = :user')
            ->setParameter('user', $this->security->getUser());

        if ($this->awardType !== '') {
            $qb->innerJoin('r.rewards', 're')
                ->innerJoin('re.award', 'a')
                ->andWhere('a.type = :type')
                ->setParameter('type', AwardType::tryFrom($this->awardType));
        }

        return $this->fetchList($qb, $this->formatItem(...));
    }

    private function formatItem(Record $item): array
    {
        return $this->normalizer->normalize($item, 'array', ['groups' => 'restful_read']);
    }
}
