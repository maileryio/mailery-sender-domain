<?php

namespace Mailery\Sender\Domain\Checker;

use Mailery\Sender\Domain\Checker\CheckerInterface;
use Mesour\DnsChecker\DnsRecordSet;
use Mailery\Sender\Domain\Field\DnsRecordType;
use Mesour\DnsChecker\IDnsRecord;
use Mailery\Sender\Domain\Entity\DnsRecord;
use Mailery\Sender\Domain\Field\DnsRecordSubType;
use SPFLib\Term\Mechanism\IncludeMechanism;
use SPFLib\Decoder;

class SpfChecker implements CheckerInterface
{
    /**
     * @param string $domainSpec
     */
    public function __construct(
        private string $domainSpec
    ) {}

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
        return DnsRecordSubType::asSpf();
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
                && strpos(strtolower($record->getContent()), 'v=spf1') === 0
                && ($spfRecord = (new Decoder())->getRecordFromTXT($record->getContent())) !== null
            ) {
                foreach ($spfRecord->getMechanisms() as $mechanism) {
                    if ($mechanism instanceof IncludeMechanism
                        && (string) $mechanism->getDomainSpec() === $this->domainSpec
                    ) {
                        return true;
                    }
                }
            }
        }

        return false;
    }
}
