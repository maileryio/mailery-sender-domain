<?php

namespace Mailery\Sender\Domain\Checker;

use Mailery\Sender\Domain\Checker\CheckerInterface;
use Mesour\DnsChecker\DnsRecordSet;
use Mesour\DnsChecker\DnsRecordType;
use Mesour\DnsChecker\IDnsRecord;
use Mailery\Sender\Domain\Enum\DnsRecordSubType;
use Mailery\Sender\Domain\Entity\DnsRecord;

class DmarcChecker implements CheckerInterface
{
    /**
     * @return string
     */
    public function getType():string
    {
        return DnsRecordType::TXT;
    }

    /**
     * @return string
     */
    public function getSubType():string
    {
        return DnsRecordSubType::DMARC;
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
            if ($record->getName() === $dnsRecord->getName()
                && strpos(strtolower($record->getContent()), 'v=dmarc1') === 0
            ) {
                return true;
            }
        }

        return false;
    }
}
