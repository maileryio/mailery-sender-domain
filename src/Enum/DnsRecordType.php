<?php

namespace Mailery\Sender\Domain\Enum;

class DnsRecordType
{
    private const TXT = 'TXT';
    private const MX = 'MX';

    /**
     * @param string $value
     */
    public function __construct(
        private string $value
    ) {}

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return static
     */
    public static function typecast(string $value): static
    {
        return new static($value);
    }

    /**
     * @return self
     */
    public static function asTxt(): self
    {
        return new self(self::TXT);
    }

    /**
     * @return self
     */
    public static function asMx(): self
    {
        return new self(self::MX);
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->getValue();
    }

    /**
     * @return bool
     */
    public function isMx(): bool
    {
        return $this->getValue() === self::MX;
    }

    /**
     * @param DnsRecordType $type
     * @return bool
     */
    public function isSame(DnsRecordType $type): bool
    {
        return $this->getValue() === $type->getValue();
    }
}