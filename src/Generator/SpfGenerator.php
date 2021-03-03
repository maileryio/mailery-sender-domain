<?php

namespace Mailery\Sender\Domain\Generator;

use SPFLib\Record;
use SPFLib\Decoder;
use SPFLib\DNS\Resolver;
use SPFLib\Term\Mechanism;
use SPFLib\Term\Mechanism\AllMechanism;
use SPFLib\Term\Mechanism\MxMechanism;
use SPFLib\Term\Mechanism\IncludeMechanism;
use Mesour\DnsChecker\DnsRecord;
use Mesour\DnsChecker\DnsRecordType;

class SpfGenerator
{
    /**
     * @var string
     */
    private string $include;

    /**
     * @var Resolver
     */
    private $dnsResolver;

    /**
     * @param string $include
     * @param Resolver $dnsResolver
     */
    public function __construct(string $include, Resolver $dnsResolver)
    {
        $this->include = $include;
        $this->dnsResolver = $dnsResolver;
    }

    /**
     * @param string $domain
     * @return DnsRecord
     */
    public function generate(string $domain): DnsRecord
    {
        try {
            $record = (new Decoder($this->dnsResolver))
                ->getRecordFromDomain($domain);
        } catch (\Exception $e) {}

        if (!empty($record)) {
            $terms = $record->getTerms();
        } else {
            $terms = [
                new MxMechanism(Mechanism::QUALIFIER_PASS),
                new AllMechanism(Mechanism::QUALIFIER_SOFTFAIL),
            ];
        }

        return new DnsRecord(
            DnsRecordType::TXT,
            $domain,
            (string) $this->applyTerms(new Record(), $terms)
        );
    }

    /**
     * @param Record $record
     * @param array $terms
     * @return Record
     */
    private function applyTerms(Record $record, array $terms): Record
    {
        $included = false;

        $fnDoInclude = function () use($record) {
            $record->addTerm(new IncludeMechanism(Mechanism::QUALIFIER_PASS, $this->include));
        };

        foreach ($terms as $term) {
            if (!$included) {
                if ($term instanceof IncludeMechanism
                    || $term instanceof AllMechanism
                ) {
                    $included = true;
                    $fnDoInclude();
                }
            }

            $record->addTerm($term);
        }

        if (!$included) {
            $fnDoInclude();
        }

        return $record;
    }
}
