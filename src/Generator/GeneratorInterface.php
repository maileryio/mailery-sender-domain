<?php

namespace Mailery\Sender\Domain\Generator;

use Mesour\DnsChecker\DnsRecord;

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
     * @param string $domain
     * @return DnsRecord
     */
    public function generate(string $domain): DnsRecord;
}