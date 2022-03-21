<?php

namespace Mailery\Sender\Domain\Checker;

use Mailery\Sender\Domain\Checker\CheckerInterface;
use Mesour\DnsChecker\DnsRecordSet;
use Mailery\Sender\Domain\Field\DnsRecordType;
use Mailery\Sender\Domain\Field\DnsRecordSubType;
use Mailery\Sender\Domain\Entity\DnsRecord;

class MxChecker implements CheckerInterface
{
    /**
     * @return DnsRecordType
     */
    public function getType(): DnsRecordType
    {
        return DnsRecordType::asMx();
    }

    /**
     * @return DnsRecordSubType
     */
    public function getSubType(): DnsRecordSubType
    {
        return DnsRecordSubType::asMx();
    }

    /**
     * @param DnsRecord $dnsRecord
     * @param DnsRecordSet $recordSet
     * @return bool
     */
    public function check(DnsRecord $dnsRecord, DnsRecordSet $recordSet): bool
    {
        if (!$dnsRecord->getType()->isSame($this->getType())
            || !$dnsRecord->getSubType()->isSame($this->getSubType())
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
