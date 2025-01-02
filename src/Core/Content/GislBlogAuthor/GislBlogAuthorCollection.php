<?php declare(strict_types=1);

namespace Gisl\GislBlog\Core\Content\GislBlogAuthor;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(GislBlogAuthorEntity $entity)
 * @method void set(string $key, GislBlogAuthorEntity $entity)
 * @method GislBlogAuthorEntity[] getIterator()
 * @method GislBlogAuthorEntity[] getElements()
 * @method GislBlogAuthorEntity|null get(string $key)
 * @method GislBlogAuthorEntity|null first()
 * @method GislBlogAuthorEntity|null last()
 */
class GislBlogAuthorCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return GislBlogAuthorEntity::class;
    }
}
