<?php

namespace Mailery\Sender\Domain\Checker;

use Mesour\DnsChecker\DnsRecordSet;
use Mailery\Sender\Domain\Enum\DnsRecordType;
use Mailery\Sender\Domain\Enum\DnsRecordSubType;
use Mailery\Sender\Domain\Entity\DnsRecord;

interface CheckerInterface
{
    /**
     * @return DnsRecordType
     */
    public function getType(): DnsRecordType;

    /**
     * @return DnsRecordSubType
     */
    public function getSubType(): DnsRecordSubType;

    /**
     * @param DnsRecord $dnsRecord
     * @param DnsRecordSet $recordSet
     * @return bool
     */
    public function check(DnsRecord $dnsRecord, DnsRecordSet $recordSet): bool;
}