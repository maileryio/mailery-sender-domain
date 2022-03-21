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
use Mailery\Sender\Domain\Field\DnsRecordType;
use Mailery\Sender\Domain\Field\DnsRecordSubType;
use Mailery\Sender\Domain\Generator\GeneratorInterface;
use Mailery\Sender\Domain\Entity\Domain;

class SpfGenerator implements GeneratorInterface
{
    /**
     * @param string $domainSpec
     * @param Resolver $dnsResolver
     */
    public function __construct(
        private string $domainSpec,
        private Resolver $dnsResolver
    ) {}

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
        return DnsRecordSubType::asSpf();
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
            $this->getType()->getValue(),
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
