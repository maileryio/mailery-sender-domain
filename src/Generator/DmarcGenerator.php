<?php

namespace Mailery\Sender\Domain\Generator;

use Mesour\DnsChecker\DnsRecord;
use Mesour\DnsChecker\DnsRecordType;
use Mailery\Sender\Domain\Enum\DnsRecordSubType;
use Mailery\Sender\Domain\Generator\GeneratorInterface;
use Mailery\Sender\Domain\Entity\Domain;

class DmarcGenerator implements GeneratorInterface
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
     * @param Domain $domain
     * @return DnsRecord
     */
    public function generate(Domain $domain): DnsRecord
    {
        return new DnsRecord(
            DnsRecordType::TXT,
            sprintf('_dmarc.%s', $domain->getDomain()),
            'v=DMARC1; p=none; rua=mailto:youremailaddress@yourdomain.tld'
        );
    }
}
