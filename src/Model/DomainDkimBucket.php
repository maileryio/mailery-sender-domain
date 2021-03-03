<?php

namespace Mailery\Sender\Domain\Model;

use Mailery\Storage\BucketInterface;
use Yiisoft\Yii\Filesystem\FilesystemInterface;
use Mailery\Brand\BrandLocator;

class DomainDkimBucket implements BucketInterface
{
    /**
     * @var FilesystemInterface
     */
    private FilesystemInterface $filesystem;

    /**
     * @var BrandLocator
     */
    private BrandLocator $brandLocator;

    /**
     * @param FilesystemInterface $filesystem
     * @param BrandLocator $brandLocator
     */
    public function __construct(FilesystemInterface $filesystem, BrandLocator $brandLocator)
    {
        $this->filesystem = $filesystem;
        $this->brandLocator = $brandLocator;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::class;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return sprintf('/%d/domain/dkim', $this->brandLocator->getBrand()->getId());
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return 'Domain DKIM';
    }

    /**
     * @return FilesystemInterface
     */
    public function getFilesystem(): FilesystemInterface
    {
        return $this->filesystem;
    }
}
