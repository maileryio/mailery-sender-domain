<?php

namespace Mailery\Sender\Domain\Generator;

use Mesour\DnsChecker\DnsRecord;
use Mailery\Sender\Domain\Entity\Domain;
use Mailery\Sender\Domain\Field\DnsRecordType;
use Mailery\Sender\Domain\Field\DnsRecordSubType;

interface GeneratorInterface
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
     * @param Domain $domain
     * @return DnsRecord
     */
    public function generate(Domain $domain): DnsRecord;
}