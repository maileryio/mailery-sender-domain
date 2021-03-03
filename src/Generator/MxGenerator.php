<?php

namespace Mailery\Sender\Domain\Generator;

use Mesour\DnsChecker\DnsRecord;
use Mesour\DnsChecker\DnsRecordType;
use Mailery\Sender\Domain\Enum\DnsRecordSubType;
use Mailery\Sender\Domain\Generator\GeneratorInterface;

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
     * @param string $domain
     * @return DnsRecord
     */
    public function generate(string $domain): DnsRecord
    {
        return new DnsRecord(DnsRecordType::MX, $domain, '');
    }
}
