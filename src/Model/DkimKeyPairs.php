<?php

namespace Mailery\Sender\Domain\Model;

use HttpSoft\Message\Stream;
use Psr\Http\Message\StreamInterface;

class DkimKeyPairs
{
    /**
     * @var StreamInterface
     */
    private StreamInterface $public;

    /**
     * @var StreamInterface
     */
    private StreamInterface $private;

    /**
     * @return StreamInterface
     */
    public function getPublic(): StreamInterface
    {
        return $this->public;
    }

    /**
     * @return StreamInterface
     */
    public function getPrivate(): StreamInterface
    {
        return $this->private;
    }

    /**
     * @param StreamInterface $public
     * @return self
     */
    public function withPublic(StreamInterface $public): self
    {
        $new = clone $this;
        $new->public = $public;

        return $new;
    }

    /**
     * @param StreamInterface $private
     * @return self
     */
    public function withPrivate(StreamInterface $private): self
    {
        $new = clone $this;
        $new->private = $private;

        return $new;
    }

    /**
     * @return self
     */
    public function generate(): self
    {
        $this->public = new Stream(tmpfile());
        $this->private = new Stream(tmpfile());

        //Create a 2048-bit RSA key with an SHA256 digest
        $pk = openssl_pkey_new([
            'digest_alg' => 'sha256',
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        openssl_pkey_export_to_file($pk, $this->private->getMetadata('uri'));
        $pkDetails = openssl_pkey_get_details($pk);
        file_put_contents($this->public->getMetadata('uri'), $pkDetails['key']);

        return $this;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        $data = (string) rand(100, 1000);

        try {
            openssl_private_encrypt($data, $encrypted, $this->private->getContents());
            openssl_public_decrypt($encrypted, $decrypted, $this->public->getContents());

            return $data === $decrypted;
        } catch (\Exception $e) {
            return false;
        }
    }
}
