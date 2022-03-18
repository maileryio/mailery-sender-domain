<?php

namespace Mailery\Sender\Domain\ValueObject;

use Mailery\Sender\Domain\Entity\Domain;
use Mailery\Storage\Entity\File;

class DkimValueObject
{
    /**
     * @var Domain
     */
    private Domain $domain;

    /**
     * @param File $public
     * @param File $private
     */
    public function __construct(
        private File $public,
        private File $private
    ) {}

    /**
     * @return File
     */
    public function getPublic(): File
    {
        return $this->public;
    }

    /**
     * @return File
     */
    public function getPrivate(): File
    {
        return $this->private;
    }

    /**
     * @return Domain
     */
    public function getDomain(): Domain
    {
        return $this->domain;
    }

    /**
     * @param Domain $domain
     * @return self
     */
    public function withDomain(Domain $domain): self
    {
        $new = clone $this;
        $new->domain = $domain;

        return $new;
    }
}
