<?php

namespace Mailery\Sender\Domain\Entity;

use Mailery\Activity\Log\Entity\LoggableEntityInterface;
use Mailery\Activity\Log\Entity\LoggableEntityTrait;
use Mailery\Sender\Domain\Entity\Domain;
use Mailery\Common\Entity\RoutableEntityInterface;
use Mesour\DnsChecker\IDnsRecord;
use Mailery\Sender\Domain\Repository\DnsRecordRepository;
use Mailery\Activity\Log\Mapper\LoggableMapper;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\ORM\Entity\Behavior;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;
use Mailery\Sender\Domain\Enum\DnsRecordStatus;
use Mailery\Sender\Domain\Enum\DnsRecordType;
use Mailery\Sender\Domain\Enum\DnsRecordSubType;

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
class DnsRecord implements RoutableEntityInterface, LoggableEntityInterface
{
    use LoggableEntityTrait;

    #[Column(type: 'primary')]
    private int $id;

    #[Column(type: 'string(255)')]
    private string $name;

    #[Column(type: 'text')]
    private string $content;

    #[BelongsTo(target: Domain::class)]
    private Domain $domain;

    #[Column(type: 'string(255)', typecast: DnsRecordType::class)]
    private DnsRecordType $type;

    #[Column(type: 'string(255)', typecast: DnsRecordSubType::class)]
    private DnsRecordSubType $subType;

    #[Column(type: 'enum(pending, found, not_found)', typecast: DnsRecordStatus::class)]
    private DnsRecordStatus $status;

    #[Column(type: 'datetime')]
    private \DateTimeImmutable $createdAt;

    #[Column(type: 'datetime', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'DNS Record #' . $this->getObjectId();
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
     * @return DnsRecordType
     */
    public function getType(): DnsRecordType
    {
        return $this->type;
    }

    /**
     * @param DnsRecordType $type
     * @return self
     */
    public function setType(DnsRecordType $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return DnsRecordSubType
     */
    public function getSubType(): DnsRecordSubType
    {
        return $this->subType;
    }

    /**
     * @param DnsRecordSubType $subType
     * @return self
     */
    public function setSubType(DnsRecordSubType $subType): self
    {
        $this->subType = $subType;

        return $this;
    }

    /**
     * @return DnsRecordStatus
     */
    public function getStatus(): DnsRecordStatus
    {
        return $this->status;
    }

    /**
     * @param DnsRecordStatus $status
     * @return self
     */
    public function setStatus(DnsRecordStatus $status): self
    {
        $this->status = $status;

        return $this;
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
