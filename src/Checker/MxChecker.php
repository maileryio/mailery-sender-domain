<?php

namespace Mailery\Sender\Domain\Checker;

use Mailery\Sender\Domain\Checker\CheckerInterface;
use Mesour\DnsChecker\DnsRecordSet;
use Mesour\DnsChecker\DnsRecordType;
use Mailery\Sender\Domain\Enum\DnsRecordSubType;

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
     * @param string $domain
     * @param DnsRecordSet $recordSet
     * @return bool
     */
    public function check(string $domain, DnsRecordSet $recordSet): bool
    {
        foreach ($recordSet->getRecordsByType($this->getType()) as $record) {
            /** @var IDnsRecord $record */
            if ($record->getName() === $domain) {
                return true;
            }
        }

        return false;
    }
}
