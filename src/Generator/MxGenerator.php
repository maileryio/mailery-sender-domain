<?php

namespace Mailery\Sender\Domain\Generator;

use Mesour\DnsChecker\DnsRecord;
use Mesour\DnsChecker\DnsRecordType;
use Mailery\Sender\Domain\Enum\DnsRecordSubType;
use Mailery\Sender\Domain\Generator\GeneratorInterface;
use Mailery\Sender\Domain\Entity\Domain;

class MxGenerator implements GeneratorInterface
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
     * @param Domain $domain
     * @return DnsRecord
     */
    public function generate(Domain $domain): DnsRecord
    {
        return new DnsRecord(DnsRecordType::MX, $domain->getDomain(), '');
    }
}
