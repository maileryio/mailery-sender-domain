<?php

namespace Mailery\Sender\Model;

use Yiisoft\Translator\TranslatorInterface;

class Status
{
    private const PENDING = 'pending';
    private const ACTIVE = 'active';
    private const INACTIVE = 'inactive';

    /**
     * @var TranslatorInterface|null
     */
    private ?TranslatorInterface $translator = null;

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
    public static function asPending(): self
    {
        return new self(self::PENDING);
    }

    /**
     * @return self
     */
    public static function asActive(): self
    {
        return new self(self::ACTIVE);
    }

    /**
     * @return self
     */
    public static function asInactive(): self
    {
        return new self(self::INACTIVE);
    }

    /**
     * @param TranslatorInterface $translator
     * @return self
     */
    public function withTranslator(TranslatorInterface $translator): self
    {
        $new = clone $this;
        $new->translator = $translator;

        return $new;
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
        $fnTranslate = function (string $message) {
            if ($this->translator !== null) {
                return $this->translator->translate($message);
            }
            return $message;
        };

        return [
            self::PENDING => $fnTranslate('Pending'),
            self::ACTIVE => $fnTranslate('Active'),
            self::INACTIVE => $fnTranslate('Inactive'),
        ][$this->value] ?? 'Unknown';
    }

    /**
     * @return string
     */
    public function getCssClass(): string
    {
        return [
            self::PENDING => 'badge-warning',
            self::ACTIVE => 'badge-success',
            self::INACTIVE => 'badge-danger',
        ][$this->value] ?? 'badge-secondary';
    }

    /**
     * @return bool
     */
    public function isPending(): bool
    {
        return $this->getValue() === self::PENDING;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->getValue() === self::ACTIVE;
    }

    /**
     * @return bool
     */
    public function isInactive(): bool
    {
        return $this->getValue() === self::INACTIVE;
    }
}
