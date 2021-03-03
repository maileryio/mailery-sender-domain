<?php

namespace Mailery\Sender\Domain\Generator;

use Mesour\DnsChecker\DnsRecord;
use Mailery\Sender\Domain\Entity\Domain;

interface GeneratorInterface
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
     * @param Domain $domain
     * @return DnsRecord
     */
    public function generate(Domain $domain): DnsRecord;
}