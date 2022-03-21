<?php

namespace Mailery\Sender\Domain\Generator;

use Mesour\DnsChecker\DnsRecord;
use Mailery\Sender\Domain\Field\DnsRecordType;
use Mailery\Sender\Domain\Field\DnsRecordSubType;
use Mailery\Sender\Domain\Generator\GeneratorInterface;
use Mailery\Sender\Domain\Entity\Domain;

class DmarcGenerator implements GeneratorInterface
{
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
        return DnsRecordSubType::asDmarc();
    }

    /**
     * @param Domain $domain
     * @return DnsRecord
     */
    public function generate(Domain $domain): DnsRecord
    {
        return new DnsRecord(
            $this->getType()->getValue(),
            sprintf('_dmarc.%s', $domain->getDomain()),
            'v=DMARC1; p=none; rua=mailto:postmaster@yourdomain.tld'
        );
    }
}
