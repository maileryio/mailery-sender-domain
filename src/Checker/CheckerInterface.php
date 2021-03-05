<?php

namespace Mailery\Sender\Domain\Checker;

use Mesour\DnsChecker\DnsRecordSet;
use Mailery\Sender\Domain\Entity\DnsRecord;

interface CheckerInterface
{
    /**
     * @return string
     */
    public function getType():string;

    /**
     * @return string
     */
    public function getSubType():string;

    /**
     * @param DnsRecord $dnsRecord
     * @param DnsRecordSet $recordSet
     * @return bool
     */
    public function check(DnsRecord $dnsRecord, DnsRecordSet $recordSet): bool;
}