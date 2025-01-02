<?php declare(strict_types=1);

namespace Gisl\GislBlog\Core\Content\GislBlogCategory;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(GislBlogCategoryEntity $entity)
 * @method void set(string $key, GislBlogCategoryEntity $entity)
 * @method GislBlogCategoryEntity[] getIterator()
 * @method GislBlogCategoryEntity[] getElements()
 * @method GislBlogCategoryEntity|null get(string $key)
 * @method GislBlogCategoryEntity|null first()
 * @method GislBlogCategoryEntity|null last()
 */
class GislBlogCategoryCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return GislBlogCategoryEntity::class;
    }
}
