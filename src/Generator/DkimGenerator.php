<?php

namespace Mailery\Sender\Domain\Generator;

use SPFLib\DNS\Resolver;
use Mesour\DnsChecker\DnsRecord;
use Mesour\DnsChecker\DnsRecordType;

class DkimGenerator
{
    /**
     * @var string
     */
    private string $selector;

    /**
     * @param string $selector
     * @param Resolver $dnsResolver
     */
    public function __construct(string $selector)
    {
        $this->selector = $selector;
    }

    /**
     * @param string $domain
     * @return DnsRecord
     */
    public function generate(string $domain): DnsRecord
    {
        return new DnsRecord(
            DnsRecordType::TXT,
            sprintf('%s._domainkey.%s', $this->selector, $domain),
            'v=DKIM1; k=rsa; p=MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCreWy3Di4fujHhYwopg+nOTEJ+6bG2hbtoIz7oP9EF6l1pzJg8CzdpFKUXMBTnKcgWML38z+XcXBRw5wjuv+eYcV0NfTMQfkmFyGE3GykTTmqwiWasTyUAoVXNlNmnfoK3nGfP5wOFU7+IT2LK+pY7ooz5tzJZiwZsOR6C0hgnzQIDAQAB'
        );
    }
}
