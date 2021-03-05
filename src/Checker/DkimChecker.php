<?php

namespace Mailery\Sender\Domain\Checker;

use Mailery\Sender\Domain\Checker\CheckerInterface;
use Mesour\DnsChecker\DnsRecordSet;
use Mesour\DnsChecker\DnsRecordType;
use Mailery\Sender\Domain\Enum\DnsRecordSubType;
use Mesour\DnsChecker\IDnsRecord;
use Mailery\Sender\Domain\Entity\DnsRecord;
use Mailery\Sender\Domain\Model\DkimKeyPairs;
use Mailery\Storage\Filesystem\FileInfo;
use HttpSoft\Message\Stream;

class DkimChecker implements CheckerInterface
{
    /**
     * @var FileInfo
     */
    private FileInfo $fileInfo;

    /**
     * @param FileInfo $fileInfo
     */
    public function __construct(FileInfo $fileInfo)
    {
        $this->fileInfo = $fileInfo;
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
     * @param DnsRecord $dnsRecord
     * @param DnsRecordSet $recordSet
     * @return bool
     */
    public function check(DnsRecord $dnsRecord, DnsRecordSet $recordSet): bool
    {
        if ($dnsRecord->getType() !== $this->getType()
            || $dnsRecord->getSubType() !== $this->getSubType()
        ) {
            return false;
        }

        foreach ($recordSet->getRecordsByType($this->getType()) as $record) {
            /** @var IDnsRecord $record */
            if ($record->getName() === $dnsRecord->getName()) {
                $stream = fopen('php://memory','r+');
                fwrite($stream, $this->preparePublicKey($record->getContent()));
                rewind($stream);

                $keyPairs = (new DkimKeyPairs())
                    ->withPublic(new Stream($stream))
                    ->withPrivate(
                        $this->fileInfo
                            ->withFile($dnsRecord->getDomain()->getDkim()->getPrivate())
                            ->getStream()
                    );

                return $keyPairs->validate();
            }
        }

        return false;
    }

    /**
     * @param string $content
     * @return string
     */
    private function preparePublicKey(string $content): string
    {
        $content = str_replace(["\r", "\n", "\"", "'"], '', $content);

        if (preg_match('/p=(.*)\b/', $content, $matches)) {
            $content = str_replace(' ', '', $matches[1]);
        }
        return "-----BEGIN PUBLIC KEY-----\n{$content}\n-----END PUBLIC KEY-----";
    }
}
