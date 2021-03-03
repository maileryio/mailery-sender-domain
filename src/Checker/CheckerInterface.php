<?php

namespace Mailery\Sender\Domain\Checker;

use Mesour\DnsChecker\DnsRecordSet;

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
     * @param string $domain
     * @param DnsRecordSet $recordSet
     * @return bool
     */
    public function check(string $domain, DnsRecordSet $recordSet): bool;
}