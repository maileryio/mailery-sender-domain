<?php

namespace Mailery\Sender\Domain\Checker;

use Mailery\Sender\Domain\Checker\CheckerInterface;
use Mesour\DnsChecker\DnsRecordSet;
use Mailery\Sender\Domain\Field\DnsRecordType;
use Mesour\DnsChecker\IDnsRecord;
use Mailery\Sender\Domain\Field\DnsRecordSubType;
use Mailery\Sender\Domain\Entity\DnsRecord;

class DmarcChecker implements CheckerInterface
{
    /**
     * @return DnsRecordType
     */
    public function getType(): DnsRecordType
    {
        return DnsRecordType::asTxt();
    }

    /**
     * @return DnsRecordSubType
     */
    public function getSubType(): DnsRecordSubType
    {
        return DnsRecordSubType::asDmarc();
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
            if ($record->getName() === $dnsRecord->getName()
                && strpos(strtolower($record->getContent()), 'v=dmarc1') === 0
            ) {
                return true;
            }
        }

        return false;
    }
}
