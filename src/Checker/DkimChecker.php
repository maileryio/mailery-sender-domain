<?php

namespace Mailery\Sender\Domain\Checker;

use Mailery\Sender\Domain\Checker\CheckerInterface;
use Mesour\DnsChecker\DnsRecordSet;
use Mesour\DnsChecker\DnsRecordType;
use Mailery\Sender\Domain\Enum\DnsRecordSubType;
use Mesour\DnsChecker\IDnsRecord;

class DkimChecker implements CheckerInterface
{
    /**
     * @var string
     */
    private string $selector;

    /**
     * @param string $selector
     */
    public function __construct(string $selector)
    {
        $this->selector = $selector;
    }

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
        return DnsRecordSubType::DKIM;
    }

    /**
     * @param string $domain
     * @param DnsRecordSet $recordSet
     * @return bool
     */
    public function check(string $domain, DnsRecordSet $recordSet): bool
    {
        $name = sprintf('%s._domainkey.%s', $this->selector, $domain);

        foreach ($recordSet->getRecordsByType($this->getType()) as $record) {
            /** @var IDnsRecord $record */
            if ($record->getName() === $name) {
                return true;
            }
        }

        return false;
    }
}
