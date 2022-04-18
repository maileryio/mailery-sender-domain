<?php

namespace Mailery\Sender\Domain\Service;

use Cycle\ORM\ORMInterface;
use Mailery\Sender\Domain\Entity\Dkim;
use Mailery\Sender\Domain\ValueObject\DkimValueObject;
use Yiisoft\Yii\Cycle\Data\Writer\EntityWriter;

class DkimCrudService
{
    /**
     * @var ORMInterface
     */
    private ORMInterface $orm;

    /**
     * @param ORMInterface $orm
     */
    public function __construct(ORMInterface $orm)
    {
        $this->orm = $orm;
    }

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

        (new EntityWriter($this->orm))->write([
            $dkim,
        ]);

        return $dkim;
    }
}
