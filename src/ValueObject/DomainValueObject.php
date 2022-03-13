<?php

namespace Mailery\Sender\Domain\ValueObject;

use Mailery\Sender\Domain\Form\DomainForm;

class DomainValueObject
{
    /**
     * @var string
     */
    private string $domain;

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
}
