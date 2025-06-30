<?php

namespace GrowthTaskBundle\Tests\Unit\Procedure;

use GrowthTaskBundle\Procedure\GetTaskRecord;
use GrowthTaskBundle\Repository\RecordRepository;
use Knp\Component\Pager\PaginatorInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class GetTaskRecordTest extends TestCase
{
    private GetTaskRecord $procedure;
    private RecordRepository&MockObject $recordRepository;
    private Security&MockObject $security;
    private NormalizerInterface&MockObject $normalizer;
    private PaginatorInterface&MockObject $paginator;

    protected function setUp(): void
    {
        $this->recordRepository = $this->createMock(RecordRepository::class);
        $this->security = $this->createMock(Security::class);
        $this->normalizer = $this->createMock(NormalizerInterface::class);
        $this->paginator = $this->createMock(PaginatorInterface::class);

        $this->procedure = new GetTaskRecord(
            $this->recordRepository,
            $this->security,
            $this->normalizer
        );
        $this->procedure->paginator = $this->paginator;
    }

    public function testInstantiation(): void
    {
        $this->assertInstanceOf(GetTaskRecord::class, $this->procedure);
    }

    public function testAwardTypeProperty(): void
    {
        $this->procedure->awardType = 'COIN';
        $this->assertEquals('COIN', $this->procedure->awardType);
    }

    public function testDefaultAwardType(): void
    {
        $this->assertEquals('', $this->procedure->awardType);
    }
}