<?php

namespace Mailery\Sender\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Mailery\Sender\Domain\Checker\CheckerInterface;
use Mailery\Sender\Domain\Enum\DnsRecordSubType;

class CheckerList extends ArrayCollection
{
    /**
     * @param DnsRecordSubType $subType
     * @return array
     */
    public function filterBySubType(DnsRecordSubType $subType): self
    {
        return $this->filter(function (CheckerInterface $checker) use($subType) {
            return $checker->getSubType()->getValue() === $subType->getValue();
        });
    }
}
