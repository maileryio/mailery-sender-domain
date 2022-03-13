<?php

namespace Mailery\Sender\Domain\Entity;

use Mailery\Activity\Log\Entity\LoggableEntityInterface;
use Mailery\Activity\Log\Entity\LoggableEntityTrait;
use Mailery\Sender\Domain\Entity\Domain;
use Mailery\Common\Entity\RoutableEntityInterface;
use Mesour\DnsChecker\DnsRecordType;
use Mesour\DnsChecker\IDnsRecord;
use Mailery\Sender\Domain\Repository\DnsRecordRepository;
use Mailery\Activity\Log\Mapper\LoggableMapper;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\ORM\Entity\Behavior;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;

#[Entity(
    table: 'domain_dns_records',
    repository: DnsRecordRepository::class,
    mapper: LoggableMapper::class
)]
#[Behavior\CreatedAt(
    field: 'createdAt',
    column: 'created_at'
)]
#[Behavior\UpdatedAt(
    field: 'updatedAt',
    column: 'updated_at'
)]
class DnsRecord implements RoutableEntityInterface, LoggableEntityInterface, IDnsRecord
{
    use LoggableEntityTrait;

    const STATUS_PENDING = 'pending';
    const STATUS_FOUND = 'found';
    const STATUS_NOT_FOUND = 'not_found';

    #[Column(type: 'primary')]
    private int $id;

    #[BelongsTo(target: Domain::class)]
    private Domain $domain;

    #[Column(type: 'string(255)')]
    private string $type;

    #[Column(type: 'string(255)')]
    private string $subType;

    #[Column(type: 'string(255)')]
    private string $name;

    #[Column(type: 'text')]
    private string $content;

    #[Column(type: 'enum(pending, found, not_found)')]
    private string $status;

    #[Column(type: 'datetime')]
    private \DateTimeImmutable $createdAt;

    #[Column(type: 'datetime', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'DNS Record #' . $this->getId();
    }

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
        $this->domain->getDnsRecords()->add($this);

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return self
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string
     */
    public function getSubType(): string
    {
        return $this->subType;
    }

    /**
     * @param string $subType
     * @return self
     */
    public function setSubType(string $subType): self
    {
        $this->subType = $subType;

        return $this;
    }


    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     * @return self
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return self
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMx(): bool
    {
        return $this->getType() === DnsRecordType::MX;
    }

    /**
     * @return bool
     */
    public function isSpf(): bool
    {
        return $this->getType() === DnsRecordType::SPF;
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->getStatus() === self::STATUS_PENDING;
    }

    /**
     * @return bool
     */
    public function isFound(): bool
    {
        return $this->getStatus() === self::STATUS_FOUND;
    }

    /**
     * @return bool
     */
    public function isNotFound(): bool
    {
        return $this->getStatus() === self::STATUS_NOT_FOUND;
    }

    /**
     * @inheritdoc
     */
    public function getIndexRouteName(): ?string
    {
        return '/brand/settings/domain';
    }

    /**
     * @inheritdoc
     */
    public function getIndexRouteParams(): array
    {
        return ['brandId' => $this->getDomain()->getBrand()->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getViewRouteName(): ?string
    {
        return '/brand/settings/domain';
    }

    /**
     * @inheritdoc
     */
    public function getViewRouteParams(): array
    {
        return ['brandId' => $this->getDomain()->getBrand()->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getEditRouteName(): ?string
    {
        return '/brand/settings/domain';
    }

    /**
     * @inheritdoc
     */
    public function getEditRouteParams(): array
    {
        return ['brandId' => $this->getDomain()->getBrand()->getId()];
    }

    /**
     * @inheritdoc
     */
    public function getDeleteRouteName(): ?string
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @inheritdoc
     */
    public function getDeleteRouteParams(): array
    {
        throw new \RuntimeException('Not implemented');
    }

    /**
     * @return int
     */
    public function getTtl(): int
    {
        return 1800;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'type' => $this->getType(),
            'name' => $this->getName(),
            'content' => $this->getContent(),
            'ttl' => $this->getTtl(),
        ];
    }
}
