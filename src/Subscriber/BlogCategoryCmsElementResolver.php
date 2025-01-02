<?php declare(strict_types=1);

namespace Gisl\GislBlog\Subscriber;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Content\Cms\DataResolver\Element\CmsElementResolverInterface;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Defaults;
use Gisl\GislBlog\Core\Content\GislBlogCategory\GislBlogCategoryDefinition;


class BlogCategoryCmsElementResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'gisl-blog-category';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $criteria = new Criteria();

        $criteria->addFilter(
            new EqualsFilter('translations.type', 'category'),
            new EqualsFilter('active', true)
        );

        $criteria->addAssociations([
            'translations',
            'media'
        ]);

        $criteriaCollection = new CriteriaCollection();

        $criteriaCollection->add(
            'gisl_blog_category',
            GislBlogCategoryDefinition::class,
            $criteria
        );

        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {

        $gislBlog = $result->get('gisl_blog_category');
        if (!$gislBlog instanceof EntitySearchResult) {
            return;
        }

        $slot->setData($gislBlog);
    }
}