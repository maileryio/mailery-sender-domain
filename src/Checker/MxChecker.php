<?php

namespace Mailery\Sender\Domain\Checker;

use Mailery\Sender\Domain\Checker\CheckerInterface;
use Mesour\DnsChecker\DnsRecordSet;
use Mesour\DnsChecker\DnsRecordType;
use Mailery\Sender\Domain\Enum\DnsRecordSubType;
use Mailery\Sender\Domain\Entity\DnsRecord;

class MxChecker implements CheckerInterface
{
    /**
     * @return string
     */
    public function getType():string
    {
        return DnsRecordType::MX;
    }

    /**
     * @return string
     */
    public function getSubType():string
    {
        return DnsRecordSubType::MX;
    }

    /**
     * @param DnsRecord $dnsRecord
     * @param DnsRecordSet $recordSet
     * @return bool
     */
    public function check(DnsRecord $dnsRecord, DnsRecordSet $recordSet): bool
    {
        if ($dnsRecord->getType() !== $this->getType()
            || $dnsRecord->getSubType() !== $this->getSubType()
        ) {
            return false;
        }

        foreach ($recordSet->getRecordsByType($this->getType()) as $record) {
            /** @var IDnsRecord $record */
            if ($record->getName() === $dnsRecord->getName()) {
                return true;
            }
        }

        return false;
    }
}
