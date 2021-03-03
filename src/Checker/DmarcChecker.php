<?php

namespace Mailery\Sender\Domain\Checker;

use Mailery\Sender\Domain\Checker\CheckerInterface;
use Mesour\DnsChecker\DnsRecordSet;
use Mesour\DnsChecker\DnsRecordType;
use Mesour\DnsChecker\IDnsRecord;
use Mailery\Sender\Domain\Enum\DnsRecordSubType;

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
     * @param string $domain
     * @param DnsRecordSet $recordSet
     * @return bool
     */
    public function check(string $domain, DnsRecordSet $recordSet): bool
    {
        $name = sprintf('_dmarc.%s', $domain);

        foreach ($recordSet->getRecordsByType($this->getType()) as $record) {
            /** @var IDnsRecord $record */
            if ($record->getName() === $name
                && strpos($record->getContent(), 'v=DMARC1') === 0
            ) {
                return true;
            }
        }

        return false;
    }
}
