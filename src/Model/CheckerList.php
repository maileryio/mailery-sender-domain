<?php

namespace Mailery\Sender\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Mailery\Sender\Domain\Checker\CheckerInterface;

class CheckerList extends ArrayCollection
{
    /**
     * @param CheckerInterface[] $checkers
     */
    public function __construct(array $checkers)
    {
        parent::__construct($checkers);
    }

    /**
     * @param string $subType
     * @return array
     */
    public function filterBySubType(string $subType): self
    {
        return $this->filter(function (CheckerInterface $checker) use($subType) {
            return $checker->getSubType() === $subType;
        });
    }
}
