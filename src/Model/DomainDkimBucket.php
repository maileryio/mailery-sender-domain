<?php

namespace Mailery\Sender\Domain\Model;

use Mailery\Storage\BucketInterface;
use Yiisoft\Yii\Filesystem\FilesystemInterface;
use Mailery\Brand\Entity\Brand;

class DomainDkimBucket implements BucketInterface
{

    /**
     * @var Brand
     */
    private Brand $brand;

    /**
     * @param FilesystemInterface $filesystem
     */
    public function __construct(
        private FilesystemInterface $filesystem
    ) {}

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
        return sprintf('/%d/domain/dkim', $this->brand->getId());
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
