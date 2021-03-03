<?php

namespace Mailery\Sender\Domain\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Mailery\Sender\Domain\Generator\GeneratorInterface;

class GeneratorList extends ArrayCollection
{
    /**
     * @param GeneratorInterface[] $generators
     */
    public function __construct(array $generators)
    {
        parent::__construct($generators);
    }
}
