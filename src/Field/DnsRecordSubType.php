<?php

namespace Mailery\Sender\Domain\Field;

class DnsRecordSubType
{
    private const SPF = 'SPF';
    private const MX = 'MX';
    private const DKIM = 'DKIM';
    private const DMARC = 'DMARC';

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
    public static function asSpf(): self
    {
        return new self(self::SPF);
    }

    /**
     * @return self
     */
    public static function asMx(): self
    {
        return new self(self::MX);
    }

    /**
     * @return self
     */
    public static function asDkim(): self
    {
        return new self(self::DKIM);
    }

    /**
     * @return self
     */
    public static function asDmarc(): self
    {
        return new self(self::DMARC);
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
     * @param DnsRecordSubType $subType
     * @return bool
     */
    public function isSame(DnsRecordSubType $subType): bool
    {
        return $this->getValue() === $subType->getValue();
    }
}