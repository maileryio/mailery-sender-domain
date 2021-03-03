<?php

namespace Mailery\Sender\Domain\Entity;

use Mailery\Activity\Log\Entity\LoggableEntityInterface;
use Mailery\Activity\Log\Entity\LoggableEntityTrait;
use Mailery\Sender\Domain\Entity\Domain;
use Mailery\Common\Entity\RoutableEntityInterface;
use Mesour\DnsChecker\DnsRecordType;
use Mesour\DnsChecker\IDnsRecord;

/**
 * @Cycle\Annotated\Annotation\Entity(
 *      table = "brand_domain_dns_records",
 *      repository = "Mailery\Sender\Domain\Repository\DnsRecordRepository",
 *      mapper = "Mailery\Sender\Domain\Mapper\DefaultMapper"
 * )
 */
class DnsRecord implements RoutableEntityInterface, LoggableEntityInterface, IDnsRecord
{
    use LoggableEntityTrait;

    const STATUS_PENDING = 'pending';
    const STATUS_FOUND = 'found';
    const STATUS_NOT_FOUND = 'not_found';

    /**
     * @Cycle\Annotated\Annotation\Column(type = "primary")
     * @var int|null
     */
    private $id;

    /**
     * @Cycle\Annotated\Annotation\Relation\BelongsTo(target = "Mailery\Sender\Domain\Entity\Domain", nullable = false)
     * @var Domain
     */
    private $domain;

    /**
     * @Cycle\Annotated\Annotation\Column(type = "string(255)")
     * @var string
     */
    private $type;

    /**
     * @Cycle\Annotated\Annotation\Column(type = "string(255)")
     * @var string
     */
    private $subtype;

    /**
     * @Cycle\Annotated\Annotation\Column(type = "string(255)")
     * @var string
     */
    private $name;

    /**
     * @Cycle\Annotated\Annotation\Column(type = "text")
     * @var string
     */
    private $content;

    /**
     * @Cycle\Annotated\Annotation\Column(type = "enum(pending, found, not_found)")
     */
    private $status;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return 'DNS Record #' . $this->getId();
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
    public function getSubtype(): string
    {
        return $this->subtype;
    }

    /**
     * @param string $subtype
     * @return self
     */
    public function setSubtype(string $subtype): self
    {
        $this->subtype = $subtype;

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
        return ['brandId' => $this->getDomain()->getBrand()->getId()];
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
        return ['brandId' => $this->getDomain()->getBrand()->getId()];
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
