<?php

namespace Mailery\Sender\Domain\Service;

use Cycle\ORM\EntityManagerInterface;
use Mailery\Sender\Domain\Entity\Dkim;
use Mailery\Sender\Domain\ValueObject\DkimValueObject;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

class DkimCrudService
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    /**
     * @param DkimValueObject $valueObject
     * @return Dkim
     */
    public function create(DkimValueObject $valueObject): Dkim
    {
        $dkim = (new Dkim())
            ->setPublic($valueObject->getPublic())
            ->setPrivate($valueObject->getPrivate())
            ->setDomain($valueObject->getDomain())
        ;

        (new EntityWriter($this->entityManager))->write([
            $dkim,
        ]);

        return $dkim;
    }
}
