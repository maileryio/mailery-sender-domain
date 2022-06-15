<?php

namespace Mailery\Sender\Domain\Field;

use Yiisoft\Translator\TranslatorInterface;

class DnsRecordStatus
{
    private const PENDING = 'pending';
    private const FOUND = 'found';
    private const NOT_FOUND = 'not_found';

    /**
     * @var TranslatorInterface|null
     */
    private ?TranslatorInterface $translator = null;

    /**
     * @param string $value
     */
    private function __construct(
        private string $value
    ) {
        if (!in_array($value, self::getKeys())) {
            throw new \InvalidArgumentException('Invalid passed value: ' . $value);
        }
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public static function getKeys(): array
    {
        return [
            self::PENDING,
            self::FOUND,
            self::NOT_FOUND,
        ];
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
    public static function asFound(): self
    {
        return new self(self::FOUND);
    }

    /**
     * @return self
     */
    public static function asNotFound(): self
    {
        return new self(self::NOT_FOUND);
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
            self::FOUND => $fnTranslate('Found'),
            self::NOT_FOUND => $fnTranslate('Not found'),
        ][$this->value] ?? 'Unknown';
    }

    /**
     * @return string
     */
    public function getCssClass(): string
    {
        return [
            self::PENDING => 'badge-warning',
            self::FOUND => 'badge-success',
            self::NOT_FOUND => 'badge-danger',
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
    public function isFound(): bool
    {
        return $this->getValue() === self::FOUND;
    }

    /**
     * @return bool
     */
    public function isNotFound(): bool
    {
        return $this->getValue() === self::NOT_FOUND;
    }
}
