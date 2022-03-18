<?php

namespace Mailery\Sender\Domain\Service;

use Mailery\Sender\Domain\Entity\DnsRecord;
use Mailery\Sender\Domain\Enum\DnsRecordStatus;
use Mesour\DnsChecker\DnsChecker;
use Mesour\DnsChecker\DnsRecordRequest;
use Mesour\DnsChecker\DnsRecordType;
use Mesour\DnsChecker\Providers\DnsRecordProvider;
use Cycle\ORM\ORMInterface;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;
use Mailery\Sender\Domain\Model\CheckerList;
use Mailery\Sender\Domain\Checker\CheckerInterface;

class DnsCheckerService
{
    /**
     * @var ORMInterface
     */
    private ORMInterface $orm;

    /**
     * @var CheckerList
     */
    private CheckerList $checkerList;

    /**
     * @param ORMInterface $orm
     */
    public function __construct(
        ORMInterface $orm,
        CheckerList $checkerList
    ) {
        $this->orm = $orm;
        $this->checkerList = $checkerList;
    }

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

        (new EntityWriter($this->orm))->write([
            ...$dnsRecords,
        ]);
    }
}
