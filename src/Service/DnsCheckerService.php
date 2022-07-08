<?php

namespace Mailery\Sender\Domain\Service;

use Mailery\Sender\Domain\Entity\DnsRecord;
use Mailery\Sender\Domain\Field\DnsRecordStatus;
use Mesour\DnsChecker\DnsChecker;
use Mesour\DnsChecker\DnsRecordRequest;
use Mesour\DnsChecker\DnsRecordType;
use Mesour\DnsChecker\Providers\DnsRecordProvider;
use Cycle\ORM\EntityManagerInterface;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;
use Mailery\Sender\Domain\Model\CheckerList;
use Mailery\Sender\Domain\Checker\CheckerInterface;

class DnsCheckerService
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private EntityManagerInterface $entityManager,
        private CheckerList $checkerList
    ) {}

    /**
     * @param string $domain
     * @param DnsRecord[] $dnsRecords
     */
    public function checkAll(string $domain, iterable $dnsRecords)
    {
        $request = new DnsRecordRequest();

        foreach ($dnsRecords as $dnsRecord) {
            $request->addFilter($dnsRecord->getName(), DnsRecordType::getPhpValue($dnsRecord->getType()->getValue()));
        }

        $provider = new DnsRecordProvider();
        $checker = new DnsChecker($provider);

        $dnsRecordSet = $checker->getDnsRecordSetFromRequest($request);

        foreach ($dnsRecords as $dnsRecord) {
            /** @var CheckerInterface $checker */
            $checker = $this->checkerList
                ->filterBySubType($dnsRecord->getSubType())
                ->first();

            if ($checker && $checker->check($dnsRecord, $dnsRecordSet)) {
                $dnsRecord->setStatus(DnsRecordStatus::asFound());
            } else {
                $dnsRecord->setStatus(DnsRecordStatus::asNotFound());
            }
        }

        (new EntityWriter($this->entityManager))->write([
            ...$dnsRecords,
        ]);
    }
}
