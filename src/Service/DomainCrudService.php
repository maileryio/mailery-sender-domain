<?php

namespace Mailery\Sender\Domain\Service;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Mailery\Sender\Domain\Entity\Domain;
use Mailery\Sender\Domain\Entity\DnsRecord;
use Mailery\Sender\Domain\ValueObject\DomainValueObject;
use Mailery\Sender\Domain\Model\GeneratorList;
use Mailery\Sender\Domain\Generator\GeneratorInterface;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

class DomainCrudService
{
    /**
     * @var ORMInterface
     */
    private ORMInterface $orm;

    /**
     * @var GeneratorList
     */
    private GeneratorList $generatorList;

    /**
     * @param ORMInterface $orm
     * @param GeneratorList $generatorList
     */
    public function __construct(
        ORMInterface $orm,
        GeneratorList $generatorList
    ) {
        $this->orm = $orm;
        $this->generatorList = $generatorList;
    }

    /**
     * @param DomainValueObject $valueObject
     * @return Domain
     */
    public function create(DomainValueObject $valueObject): Domain
    {
        $domain = (new Domain())
            ->setDomain($valueObject->getDomain())
            ->setBrand($valueObject->getBrand())
        ;

        (new EntityWriter($this->orm))->write([
            $domain,
            ...($this->buildDnsRecords($domain)),
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
        $domain = $domain
            ->setDomain($valueObject->getDomain())
        ;

        $transaction = new Transaction($this->orm);
        $transaction->persist($domain);

        foreach ($domain->getDnsRecords() as $entity) {
            $transaction->delete($entity);
        }

        foreach ($this->buildDnsRecords($domain) as $entity) {
            $transaction->persist($entity);
        }

        $transaction->run();

        return $domain;
    }

    /**
     * @param Domain $domain
     * @return void
     */
    public function delete(Domain $domain): void
    {
        (new EntityWriter($this->orm))->delete([
            ...($domain->getDnsRecords()->toArray()),
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
                $dnsRecord = $generator->generate($domain->getDomain());

                return (new DnsRecord())
                    ->setDomain($domain)
                    ->setType($generator->getType())
                    ->setSubtype($generator->getSubType())
                    ->setName($dnsRecord->getName())
                    ->setContent($dnsRecord->getContent())
                    ->setStatus(DnsRecord::STATUS_PENDING);
            })
            ->toArray();
    }
}
