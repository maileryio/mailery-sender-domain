<?php

namespace Mailery\Sender\Domain\Checker;

use Mailery\Sender\Domain\Checker\CheckerInterface;
use Mesour\DnsChecker\DnsRecordSet;
use Mesour\DnsChecker\DnsRecordType;
use Mesour\DnsChecker\IDnsRecord;
use Mailery\Sender\Domain\Enum\DnsRecordSubType;
use SPFLib\Record;
use SPFLib\Term\Mechanism;
use SPFLib\Term\Mechanism\IncludeMechanism;

class SpfChecker implements CheckerInterface
{
    /**
     * @var string
     */
    private string $domainSpec;

    /**
     * @param string $domainSpec
     */
    public function __construct(string $domainSpec)
    {
        $this->domainSpec = $domainSpec;
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
        return DnsRecordSubType::SPF;
    }

    /**
     * @param string $domain
     * @param DnsRecordSet $recordSet
     * @return bool
     */
    public function check(string $domain, DnsRecordSet $recordSet): bool
    {
        $include = (string) (new IncludeMechanism(Mechanism::QUALIFIER_PASS, $this->domainSpec));

        foreach ($recordSet->getRecordsByType($this->getType()) as $record) {
            /** @var IDnsRecord $record */
            if ($record->getName() === $domain
                && strpos($record->getContent(), Record::PREFIX) === 0
                && strpos($record->getContent(), $include) !== false
            ) {
                return true;
            }
        }

        return false;
    }
}
