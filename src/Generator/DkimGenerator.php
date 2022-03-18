<?php

namespace Mailery\Sender\Domain\Generator;

use Mesour\DnsChecker\DnsRecord;
use Mailery\Sender\Domain\Enum\DnsRecordType;
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
     * @param string $selector
     * @param MimeTypes $mimeTypes
     * @param FileInfo $fileInfo
     * @param DomainDkimBucket $bucket
     * @param StorageService $storageService
     * @param DkimCrudService $dkimCrudService
     */
    public function __construct(
        private string $selector,
        private MimeTypes $mimeTypes,
        private FileInfo $fileInfo,
        private DomainDkimBucket $bucket,
        private StorageService $storageService,
        private DkimCrudService $dkimCrudService
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
        return DnsRecordSubType::asDkim();
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
            $this->getType()->getValue(),
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

        return $this->storageService
            ->withBrand($domain->getBrand())
            ->create(
                (new FileValueObject(
                    $title,
                    $mimeTypes[0] ?? 'text/plain',
                    $stream
                ))->withBucket($this->bucket)
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
