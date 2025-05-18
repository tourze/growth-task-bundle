<?php

namespace GrowthTaskBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class GetCreditPointEvent extends Event
{
    private ?string $pointStr = null;

    public function getPointStr(): ?string
    {
        return $this->pointStr;
    }

    public function setPointStr(?string $pointStr): void
    {
        $this->pointStr = $pointStr;
    }
}
