<?php

namespace Mailery\Sender\Domain\Entity;

use Mailery\Activity\Log\Entity\LoggableEntityInterface;
use Mailery\Activity\Log\Entity\LoggableEntityTrait;
use Mailery\Brand\Entity\Brand;
use Mailery\Common\Entity\RoutableEntityInterface;
use Mailery\Sender\Domain\Entity\Dkim;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Mailery\Sender\Domain\Repository\DomainRepository;
use Mailery\Activity\Log\Mapper\LoggableMapper;
use Cycle\ORM\Entity\Behavior;
use Cycle\Annotated\Annotation\Entity;
use Cycle\Annotated\Annotation\Column;
use Cycle\Annotated\Annotation\Relation\BelongsTo;
use Cycle\Annotated\Annotation\Relation\HasMany;
use Mailery\Sender\Domain\Entity\DnsRecord;
use Cycle\ORM\Collection\DoctrineCollectionFactory;
use Cycle\Annotated\Annotation\Relation\HasOne;

#[Entity(
    table: 'domains',
    repository: DomainRepository::class,
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
class Domain implements RoutableEntityInterface, LoggableEntityInterface
{
    use LoggableEntityTrait;

    #[Column(type: 'primary')]
    private int $id;

    #[BelongsTo(target: Brand::class)]
    private Brand $brand;

    #[Column(type: 'string(255)')]
    private string $domain;

    #[HasMany(target: DnsRecord::class, collection: DoctrineCollectionFactory::class)]
    private ArrayCollection $dnsRecords;

    #[HasOne(target: Dkim::class)]
    private Dkim $dkim;

    #[Column(type: 'datetime')]
    private \DateTimeImmutable $createdAt;

    #[Column(type: 'datetime', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->dnsRecords = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->getDomain();
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
     * @return Brand
     */
    public function getBrand(): Brand
    {
        return $this->brand;
    }

    /**
     * @param Brand $brand
     * @return self
     */
    public function setBrand(Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
    }

    /**
     * @param string $domain
     * @return self
     */
    public function setDomain(string $domain): self
    {
        $this->domain = $domain;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getDnsRecords(): Collection
    {
        return $this->dnsRecords;
    }

    /**
     * @param Collection $dnsRecords
     * @return self
     */
    public function setDnsRecords(Collection $dnsRecords): self
    {
        $this->dnsRecords = $dnsRecords;

        return $this;
    }

    /**
     * @return Dkim
     */
    public function getDkim(): Dkim
    {
        return $this->dkim;
    }

    /**
     * @param Dkim $dkim
     * @return self
     */
    public function setDkim(Dkim $dkim): self
    {
        $this->dkim = $dkim;

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
        return ['brandId' => $this->getBrand()->getId()];
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
        return ['brandId' => $this->getBrand()->getId()];
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
     * @return bool
     */
    public function isVerified(): bool
    {
        if ($this->getDnsRecords()->isEmpty()) {
            return false;
        }

        return $this->getDnsRecords()->filter(function (DnsRecord $dnsRecord) {
            return !$dnsRecord->getStatus()->isFound();
        })->isEmpty();
    }
}
