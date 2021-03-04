<?php

namespace Mailery\Sender\Domain\Entity;

use Mailery\Activity\Log\Entity\LoggableEntityInterface;
use Mailery\Activity\Log\Entity\LoggableEntityTrait;
use Mailery\Brand\Entity\Brand;
use Mailery\Common\Entity\RoutableEntityInterface;
use Mailery\Sender\Domain\Entity\Dkim;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Cycle\Annotated\Annotation\Entity(
 *      table = "domains",
 *      repository = "Mailery\Sender\Domain\Repository\DomainRepository",
 *      mapper = "Mailery\Sender\Domain\Mapper\DefaultMapper"
 * )
 */
class Domain implements RoutableEntityInterface, LoggableEntityInterface
{
    use LoggableEntityTrait;

    /**
     * @Cycle\Annotated\Annotation\Column(type = "primary")
     * @var int|null
     */
    private $id;

    /**
     * @Cycle\Annotated\Annotation\Relation\BelongsTo(target = "Mailery\Brand\Entity\Brand", nullable = false)
     * @var Brand
     */
    private $brand;

    /**
     * @Cycle\Annotated\Annotation\Column(type = "string(255)")
     * @var string
     */
    private $domain;

    /**
     * @Cycle\Annotated\Annotation\Relation\HasMany(target = "Mailery\Sender\Domain\Entity\DnsRecord")
     * @var Collection
     */
    private $dnsRecords;

    /**
     * @Cycle\Annotated\Annotation\Relation\HasOne(target = "Mailery\Sender\Domain\Entity\Dkim", nullable = false)
     * @var Dkim
     */
    private $dkim;

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
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id ? (string) $this->id : null;
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
     * {@inheritdoc}
     */
    public function getEditRouteName(): ?string
    {
        return '/brand/settings/domain';
    }

    /**
     * {@inheritdoc}
     */
    public function getEditRouteParams(): array
    {
        return ['brandId' => $this->getBrand()->getId()];
    }

    /**
     * {@inheritdoc}
     */
    public function getViewRouteName(): ?string
    {
        return '/brand/settings/domain';
    }

    /**
     * {@inheritdoc}
     */
    public function getViewRouteParams(): array
    {
        return ['brandId' => $this->getBrand()->getId()];
    }
}
