<?php

namespace Mailery\Sender\Domain\Entity;

use Mailery\Storage\Entity\File;
use Mailery\Activity\Log\Entity\LoggableEntityInterface;
use Mailery\Activity\Log\Entity\LoggableEntityTrait;
use Mailery\Sender\Domain\Entity\Domain;
use Mailery\Activity\Log\Mapper\LoggableMapper;
use Cycle\ORM\Entity\Behavior;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Relation\BelongsTo;

#[Entity(
    table: 'domain_dkim',
    mapper: LoggableMapper::class,
)]
#[Behavior\CreatedAt(
    field: 'createdAt',
    column: 'created_at'
)]
#[Behavior\UpdatedAt(
    field: 'updatedAt',
    column: 'updated_at'
)]
class Dkim implements LoggableEntityInterface
{
    use LoggableEntityTrait;

    #[Column(type: 'primary')]
    private int $id;

    #[BelongsTo(target: File::class, nullable: true)]
    private ?File $public = null;

    #[BelongsTo(target: File::class, nullable: true)]
    private ?File $private = null;

    #[BelongsTo(target: Domain::class)]
    private Domain $domain;

    #[Column(type: 'datetime')]
    private \DateTimeImmutable $createdAt;

    #[Column(type: 'datetime', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

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
