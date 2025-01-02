<?php declare(strict_types=1);

namespace Gisl\GislBlog\Core\Content\GislBlogPost;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(GislBlogPostEntity $entity)
 * @method void set(string $key, GislBlogPostEntity $entity)
 * @method GislBlogPostEntity[] getIterator()
 * @method GislBlogPostEntity[] getElements()
 * @method GislBlogPostEntity|null get(string $key)
 * @method GislBlogPostEntity|null first()
 * @method GislBlogPostEntity|null last()
 */
class GislBlogPostCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return GislBlogPostEntity::class;
    }
}
