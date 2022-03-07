<?php

namespace Mailery\Sender\Domain\ValueObject;

use Mailery\Brand\Entity\Brand;
use Mailery\Sender\Domain\Form\DomainForm;

class DomainValueObject
{
    /**
     * @var string
     */
    private string $domain;

    /**
     * @var Brand
     */
    private Brand $brand;

    /**
     * @param DomainForm $form
     * @return self
     */
    public static function fromForm(DomainForm $form): self
    {
        $new = new self();
        $new->domain = $form->getDomain();

        return $new;
    }

    /**
     * @return string
     */
    public function getDomain(): string
    {
        return $this->domain;
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
    public function withBrand(Brand $brand): self
    {
        $new = clone $this;
        $new->brand = $brand;

        return $new;
    }
}
