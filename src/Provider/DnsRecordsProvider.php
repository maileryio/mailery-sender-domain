<?php

namespace Mailery\Sender\Domain\Provider;

use Mailery\Sender\Domain\Entity\Domain;
use Mailery\Sender\Domain\Generator\SpfGenerator;
use Mailery\Sender\Domain\Generator\DkimGenerator;
use Mesour\DnsChecker\DnsRecord;
use Mesour\DnsChecker\DnsRecordSet;
use Mesour\DnsChecker\DnsRecordType;

class DnsRecordsProvider
{
    /**
     * @var SpfGenerator
     */
    private SpfGenerator $spfGenerator;

    /**
     * @var DkimGenerator
     */
    private DkimGenerator $dkimGenerator;

    /**
     * @param SpfGenerator $spfGenerator
     * @param DkimGenerator $dkimGenerator
     */
    public function __construct(
        SpfGenerator $spfGenerator,
        DkimGenerator $dkimGenerator
    ) {
        $this->spfGenerator = $spfGenerator;
        $this->dkimGenerator = $dkimGenerator;
    }

    /**
     * @param Domain $domain
     * @return array
     */
    public function getExpected(Domain $domain): DnsRecordSet
    {
        return new DnsRecordSet([
            'SPF' => $this->getSpfRecord($domain),
            'DKIM' => $this->getDkimRecord($domain),
            'DMARC' => $this->getDmarcRecord($domain),
            'MX' => $this->getMxRecord($domain),
        ]);
    }

    /**
     * @param Domain $domain
     * @return DnsRecordSet
     */
    public function getSpfRecord(Domain $domain): DnsRecord
    {
        return $this->spfGenerator->generate($domain->getDomain());
    }

    /**
     * @param Domain $domain
     * @return DnsRecordSet
     */
    public function getDkimRecord(Domain $domain): DnsRecord
    {
        return $this->dkimGenerator->generate($domain->getDomain());
    }

    /**
     * @param Domain $domain
     * @return DnsRecordSet
     */
    public function getDmarcRecord(Domain $domain): DnsRecord
    {
        return new DnsRecord(
            DnsRecordType::TXT,
            sprintf('_dmarc.%s', $domain->getDomain()),
            'v=DMARC1; p=none; rua=mailto:support@automotolife.com'
        );
    }

    /**
     * @param Domain $domain
     * @return DnsRecordSet
     */
    public function getMxRecord(Domain $domain): DnsRecord
    {
        return new DnsRecord(
            DnsRecordType::MX,
            $domain->getDomain(),
            ''
        );
    }
}
