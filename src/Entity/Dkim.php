<?php

namespace Mailery\Sender\Domain\Entity;

use Mailery\Storage\Entity\File;
use Mailery\Activity\Log\Entity\LoggableEntityInterface;
use Mailery\Activity\Log\Entity\LoggableEntityTrait;
use Mailery\Sender\Domain\Entity\Domain;

/**
 * @Cycle\Annotated\Annotation\Entity(
 *      table = "domain_dkim",
 *      mapper = "Mailery\Sender\Domain\Mapper\DefaultMapper"
 * )
 */
class Dkim implements LoggableEntityInterface
{
    use LoggableEntityTrait;

    /**
     * @Cycle\Annotated\Annotation\Column(type = "primary")
     * @var int|null
     */
    private $id;

    /**
     * @Cycle\Annotated\Annotation\Relation\BelongsTo(target = "Mailery\Storage\Entity\File", nullable = true)
     * @var File
     */
    private $public;

    /**
     * @Cycle\Annotated\Annotation\Relation\BelongsTo(target = "Mailery\Storage\Entity\File", nullable = true)
     * @var File
     */
    private $private;

    /**
     * @Cycle\Annotated\Annotation\Relation\BelongsTo(target = "Mailery\Sender\Domain\Entity\Domain", nullable = false)
     * @var Domain
     */
    private $domain;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return File
     */
    public function getPublic(): File
    {
        return $this->public;
    }

    /**
     * @param File $public
     * @return self
     */
    public function setPublic(File $public): self
    {
        $this->public = $public;

        return $this;
    }

    /**
     * @return File
     */
    public function getPrivate(): File
    {
        return $this->private;
    }

    /**
     * @param File $private
     * @return self
     */
    public function setPrivate(File $private): self
    {
        $this->private = $private;

        return $this;
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
    public function setDomain(Domain $domain): self
    {
        $this->domain = $domain;
        $this->domain->setDkim($this);

        return $this;
    }
}
