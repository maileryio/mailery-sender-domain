<?php

namespace Mailery\Sender\Domain\Generator;

use Mesour\DnsChecker\DnsRecord;
use Mesour\DnsChecker\DnsRecordType;
use Mailery\Sender\Domain\Enum\DnsRecordSubType;
use Mailery\Sender\Domain\Generator\GeneratorInterface;
use Mailery\Storage\Service\StorageService;
use Mailery\Storage\ValueObject\FileValueObject;
use Mailery\Sender\Domain\Entity\Domain;
use Mailery\Sender\Domain\Model\DomainDkimBucket;
use Mailery\Sender\Domain\Service\DkimCrudService;
use Mailery\Sender\Domain\ValueObject\DkimValueObject;
use Mailery\Storage\Entity\File;
use Mailery\Storage\Filesystem\FileInfo;
use Symfony\Component\Mime\MimeTypes;
use HttpSoft\Message\Stream;
use Mailery\Sender\Domain\Model\DkimKeyPairs;

class DkimGenerator implements GeneratorInterface
{
    /**
     * @var string
     */
    private string $selector;

    /**
     * @var MimeTypes
     */
    private MimeTypes $mimeTypes;

    /**
     * @var FileInfo
     */
    private FileInfo $fileInfo;

    /**
     * @var DomainDkimBucket
     */
    private DomainDkimBucket $bucket;

    /**
     * @var StorageService
     */
    private StorageService $storageService;

    /**
     * @var DkimCrudService
     */
    private DkimCrudService $dkimCrudService;

    /**
     * @param string $selector
     * @param MimeTypes $mimeTypes
     * @param FileInfo $fileInfo
     * @param DomainDkimBucket $bucket
     * @param StorageService $storageService
     * @param DkimCrudService $dkimCrudService
     */
    public function __construct(
        string $selector,
        MimeTypes $mimeTypes,
        FileInfo $fileInfo,
        DomainDkimBucket $bucket,
        StorageService $storageService,
        DkimCrudService $dkimCrudService
    ) {
        $this->selector = $selector;
        $this->mimeTypes = $mimeTypes;
        $this->fileInfo = $fileInfo;
        $this->bucket = $bucket;
        $this->storageService = $storageService;
        $this->dkimCrudService = $dkimCrudService;
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
        return DnsRecordSubType::DKIM;
    }

    /**
     * @param Domain $domain
     * @return DnsRecord
     */
    public function generate(Domain $domain): DnsRecord
    {
        $keyPairs = (new DkimKeyPairs())->generate();

        $dkim = $this->dkimCrudService->create(
            (new DkimValueObject(
                $this->createFile($domain, 'DKIM public key', $keyPairs->getPublic()),
                $this->createFile($domain, 'DKIM private key', $keyPairs->getPrivate())
            ))
                ->withDomain($domain)
        );

        $publicKeyContent = $this->fileInfo
            ->withFile($dkim->getPublic())
            ->getStream()
            ->getContents();

        return new DnsRecord(
            DnsRecordType::TXT,
            sprintf('%s._domainkey.%s', $this->selector, $domain->getDomain()),
            $this->preparePublicKey($publicKeyContent)
        );
    }

    /**
     * @param Domain $domain
     * @param string $title
     * @param Stream $stream
     * @return File
     */
    private function createFile(Domain $domain, string $title, Stream $stream): File
    {
        $mimeTypes = $this->mimeTypes->getMimeTypes('pem');

        return $this->storageService->create(
            (new FileValueObject(
                $title,
                $mimeTypes[0] ?? 'text/plain',
                $stream
            ))
                ->withBrand($domain->getBrand())
                ->withBucket($this->bucket)
        );
    }

    /**
     * @param string $content
     * @return string
     */
    private function preparePublicKey(string $content): string
    {
        $dnsValue = '"v=DKIM1; h=sha256; t=s; p=" ';

        //Remove PEM wrapper
        $content = preg_replace('/^-+.*?-+$/m', '', $content);
        //Strip line breaks
        $content = str_replace(["\r", "\n"], '', $content);

        //Strip and split the key into smaller parts and format for DNS
        //Many DNS systems don't like long TXT entries
        //but are OK if it's split into 255-char chunks
        foreach (str_split($content, 253) as $part) {
            $dnsValue .= '"' . trim($part) . '" ';
        }

        return trim($dnsValue);
    }
}
