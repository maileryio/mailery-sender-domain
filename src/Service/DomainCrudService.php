<?php

namespace Mailery\Sender\Domain\Service;

use Cycle\ORM\ORMInterface;
use Mailery\Sender\Domain\Entity\Domain;
use Mailery\Sender\Domain\Entity\DnsRecord;
use Mailery\Sender\Domain\Enum\DnsRecordStatus;
use Mailery\Sender\Domain\ValueObject\DomainValueObject;
use Mailery\Sender\Domain\Model\GeneratorList;
use Mailery\Sender\Domain\Generator\GeneratorInterface;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;
use Mailery\Brand\Entity\Brand;

class DomainCrudService
{
    /**
     * @var Brand
     */
    private Brand $brand;

    /**
     * @param ORMInterface $orm
     * @param GeneratorList $generatorList
     */
    public function __construct(
        private ORMInterface $orm,
        private GeneratorList $generatorList
    ) {}

    /**
     * @param Brand $brand
     * @return self
     */
    public function withBrand(Brand $brand): self
    {
        $new = clone $this;
        $new->brand = $brand;

        return $new;
    }

    /**
     * @param DomainValueObject $valueObject
     * @return Domain
     */
    public function create(DomainValueObject $valueObject): Domain
    {
        $domain = (new Domain())
            ->setDomain($valueObject->getDomain())
            ->setBrand($this->brand)
        ;

        $this->buildDnsRecords($domain);

        (new EntityWriter($this->orm))->write([
            $domain,
            ...$domain->getDnsRecords(),
        ]);

        return $domain;
    }

    /**
     * @param Domain $domain
     * @param DomainValueObject $valueObject
     * @return Domain
     */
    public function update(Domain $domain, DomainValueObject $valueObject): Domain
    {
        if ($valueObject->getDomain() === $domain->getDomain()) {
            return $domain;
        }

        $this->delete($domain);
        return $this->create($valueObject);
    }

    /**
     * @param Domain $domain
     * @return void
     */
    public function delete(Domain $domain): void
    {
        (new EntityWriter($this->orm))->delete([
            ...$domain->getDnsRecords()->toArray(),
            $domain,
        ]);
    }

    /**
     * @param Domain $domain
     * @return DnsRecord[]
     */
    private function buildDnsRecords(Domain $domain): array
    {
        return $this->generatorList
            ->map(function (GeneratorInterface $generator) use($domain) {
                $dnsRecord = $generator->generate($domain);

                return (new DnsRecord())
                    ->setDomain($domain)
                    ->setType($generator->getType())
                    ->setSubtype($generator->getSubType())
                    ->setName($dnsRecord->getName())
                    ->setContent($dnsRecord->getContent())
                    ->setStatus(DnsRecordStatus::asPending());
            })
            ->toArray();
    }
}
