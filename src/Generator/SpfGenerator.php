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
use Mailery\Sender\Domain\Enum\DnsRecordSubType;
use Mailery\Sender\Domain\Generator\GeneratorInterface;
use Mailery\Sender\Domain\Entity\Domain;

class SpfGenerator implements GeneratorInterface
{
    /**
     * @var string
     */
    private string $domainSpec;

    /**
     * @var Resolver
     */
    private Resolver $dnsResolver;

    /**
     * @param string $domainSpec
     * @param Resolver $dnsResolver
     */
    public function __construct(string $domainSpec, Resolver $dnsResolver)
    {
        $this->domainSpec = $domainSpec;
        $this->dnsResolver = $dnsResolver;
    }

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
        return DnsRecordSubType::SPF;
    }

    /**
     * @param Domain $domain
     * @return DnsRecord
     */
    public function generate(Domain $domain): DnsRecord
    {
        try {
            $record = (new Decoder($this->dnsResolver))
                ->getRecordFromDomain($domain->getDomain());
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
            $domain->getDomain(),
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
            $record->addTerm(new IncludeMechanism(Mechanism::QUALIFIER_PASS, $this->domainSpec));
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
