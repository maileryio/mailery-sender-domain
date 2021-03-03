<?php

namespace Mailery\Sender\Domain\Service;

use Mailery\Sender\Domain\Entity\DnsRecord as DnsRecordEntity;
use Mesour\DnsChecker\DnsChecker;
use Mesour\DnsChecker\DnsRecordRequest;
use Mesour\DnsChecker\DnsRecordType;
use Mesour\DnsChecker\Providers\DnsRecordProvider;
use Cycle\ORM\ORMInterface;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

class DnsCheckerService
{
    /**
     * @var ORMInterface
     */
    private ORMInterface $orm;

    /**
     * @param ORMInterface $orm
     */
    public function __construct(ORMInterface $orm)
    {
        $this->orm = $orm;
    }

    /**
     * @param DnsRecordEntity[] $dnsRecords
     */
    public function checkAll(iterable $dnsRecords)
    {
        $request = new DnsRecordRequest();

        foreach ($dnsRecords as $dnsRecord) {
            $request->addFilter($dnsRecord->getName(), DnsRecordType::getPhpValue($dnsRecord->getType()));
        }

        $provider = new DnsRecordProvider();
        $checker = new DnsChecker($provider);

        $dnsRecordSet = $checker->getDnsRecordSetFromRequest($request);

        foreach ($dnsRecords as $dnsRecord) {
            if (!$dnsRecordSet->hasRecord($dnsRecord)) {
                $dnsRecord->setStatus(DnsRecordEntity::STATUS_NOT_FOUND);
                continue;
            }

            if ($dnsRecord->isMx() && $dnsRecordSet->getRecordsByType($dnsRecord->getType()) > 0) {
                $dnsRecord->setStatus(DnsRecordEntity::STATUS_FOUND);
                continue;
            }

            if ($dnsRecord->isSpf() && $dnsRecordSet->getRecordsByType($dnsRecord->getType()) > 0) {
                foreach ($dnsRecordSet->getRecordsByType($dnsRecord->getType()) as $spfDnsRecord) {
                    ;
                }

                $dnsRecord->setStatus(DnsRecordEntity::STATUS_FOUND);
            }

            if ($dnsRecordSet->hasRecord($dnsRecord)) {
                $dnsRecord->setStatus(DnsRecordEntity::STATUS_FOUND);
            } else if ($dnsRecord->isMx() && $dnsRecordSet->getRecordsByType($dnsRecord->getType()) > 0) {
                $dnsRecord->setStatus(DnsRecordEntity::STATUS_FOUND);
            } else {
                $dnsRecord->setStatus(DnsRecordEntity::STATUS_NOT_FOUND);
            }
        }

        (new EntityWriter($this->orm))->write([
            ...$dnsRecords,
        ]);
    }
}
