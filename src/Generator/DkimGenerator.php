<?php

namespace Mailery\Sender\Domain\Generator;

use Mesour\DnsChecker\DnsRecord;
use Mesour\DnsChecker\DnsRecordType;
use Mailery\Sender\Domain\Enum\DnsRecordSubType;
use Mailery\Sender\Domain\Generator\GeneratorInterface;
use Mailery\Storage\Service\StorageService;
use Mailery\Storage\ValueObject\FileValueObject;

class DkimGenerator implements GeneratorInterface
{
    /**
     * @var string
     */
    private string $selector;

    /**
     * @var StorageService
     */
    private StorageService $storageService;

    /**
     * @param string $selector
     * @param StorageService $storageService
     */
    public function __construct(string $selector, StorageService $storageService)
    {
        $this->selector = $selector;
        $this->storageService = $storageService;
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
     * @param string $domain
     * @return DnsRecord
     */
    public function generate(string $domain): DnsRecord
    {
        $privKeyFile = tmpfile();
        $privKeyFilePath = stream_get_meta_data(tmpfile())['uri'];

        $pubKeyFile = tmpfile();
        $pubKeyFilePath = stream_get_meta_data(tmpfile())['uri'];

        //Create a 2048-bit RSA key with an SHA256 digest
        $pk = openssl_pkey_new([
            'digest_alg' => 'sha256',
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        openssl_pkey_export_to_file($pk, $privKeyFilePath);
        $pkDetails = openssl_pkey_get_details($pk);
        file_put_contents($pubKeyFilePath, $pkDetails['key']);

        $this->storageService->create(
            (new FileValueObject())
                ->withBrand($valueObject->getBrand())
                ->withBucket($this->bucket)
        );
        var_dump($privKeyFilePath, $pubKeyFilePath);exit;

        return new DnsRecord(
            DnsRecordType::TXT,
            sprintf('%s._domainkey.%s', $this->selector, $domain),
            'v=DKIM1; k=rsa; p=MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCreWy3Di4fujHhYwopg+nOTEJ+6bG2hbtoIz7oP9EF6l1pzJg8CzdpFKUXMBTnKcgWML38z+XcXBRw5wjuv+eYcV0NfTMQfkmFyGE3GykTTmqwiWasTyUAoVXNlNmnfoK3nGfP5wOFU7+IT2LK+pY7ooz5tzJZiwZsOR6C0hgnzQIDAQAB'
        );
    }
}
