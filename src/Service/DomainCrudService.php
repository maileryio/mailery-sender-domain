<?php

namespace Mailery\Sender\Domain\Service;

use Cycle\ORM\ORMInterface;
use Cycle\ORM\Transaction;
use Mailery\Sender\Domain\Entity\Domain;
use Mailery\Sender\Domain\Entity\DnsRecord;
use Mailery\Sender\Domain\ValueObject\DomainValueObject;
use Mailery\Sender\Domain\Provider\DnsRecordsProvider;
use Mesour\DnsChecker\DnsRecord as MesourDnsRecord;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

class DomainCrudService
{
    /**
     * @var ORMInterface
     */
    private ORMInterface $orm;

    /**
     * @var DnsRecordsProvider
     */
    private DnsRecordsProvider $dnsRecordsProvider;

    /**
     * @param ORMInterface $orm
     * @param DnsRecordsProvider $dnsRecordsProvider
     */
    public function __construct(
        ORMInterface $orm,
        DnsRecordsProvider $dnsRecordsProvider
    ) {
        $this->orm = $orm;
        $this->dnsRecordsProvider = $dnsRecordsProvider;
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
        $dnsRecords = [];
        $dnsRecordsSet = $this->dnsRecordsProvider->getExpected($domain);

        foreach ($dnsRecordsSet->getRecords() as $subtype => $item) {
            /** @var MesourDnsRecord $item */
            $dnsRecords[] = (new DnsRecord)
                ->setDomain($domain)
                ->setType($item->getType())
                ->setSubtype($subtype)
                ->setName($item->getName())
                ->setContent($item->getContent())
                ->setStatus(DnsRecord::STATUS_PENDING);
        }

        return $dnsRecords;
    }
}
