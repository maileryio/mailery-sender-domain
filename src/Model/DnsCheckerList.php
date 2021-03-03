<?php

namespace Mailery\Sender\Domain\Model;

use Mailery\Sender\Domain\Entity\DnsRecord;

class DnsCheckerList
{
    /**
     * @var array
     */
    private array $checkers;

    /**
     * @param array $checkers
     */
    public function __construct(array $checkers)
    {
        $this->checkers = $checkers;
    }

    /**
     * @param DnsRecord[] $dnsRecords
     */
    public function checkAll(iterable $dnsRecords)
    {
        ;
    }
}
