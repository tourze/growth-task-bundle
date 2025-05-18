<?php

namespace GrowthTaskBundle\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use GrowthTaskBundle\Repository\RewardRepository;
use LotteryBundle\Event\AfterChanceExpireEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * 处理抽奖
 */
class ChanceSubscriber
{
    public function __construct(
        private readonly RewardRepository $rewardRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[AsEventListener]
    public function onExpireChance(AfterChanceExpireEvent $event): void
    {
        $reward = $this->rewardRepository->findOneBy([
            'user' => $event->getChance()->getUser(),
            'value' => $event->getChance()->getId(),
        ]);
        if (empty($reward)) {
            return;
        }

        $reward->setCanUse($event->getChance()->getValid());
        $this->entityManager->persist($reward);
        $this->entityManager->flush();
    }
}
