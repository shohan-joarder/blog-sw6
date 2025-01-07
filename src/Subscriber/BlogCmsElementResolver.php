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
use Gisl\GislBlog\Core\Content\GislBlogPost\GislBlogPostDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\CustomFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;

use Shopware\Core\Defaults;


class BlogCmsElementResolver extends AbstractCmsElementResolver
{
    public function getType(): string
    {
        return 'gisl-blog-listing';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $request = $resolverContext->getRequest();

        $categoryId = $request->query->get("category");

        $criteria = new Criteria();

        $dateTime = new \DateTime();

        $criteria->addFilter(
            new EqualsFilter('translations.type', 'blog'),
            new EqualsFilter('active', true),
            new RangeFilter('publishedAt', [RangeFilter::LTE => $dateTime->format(\DATE_ATOM)])
        );

        if ($categoryId) {
            $criteria->addFilter(
                new ContainsFilter('categories', $categoryId)
            );
        }
        
        $criteria->addAssociations([
            'translations',
            'postAuthor.media',
            'media'
        ]);

        $criteria->addSorting(new FieldSorting('publishedAt', FieldSorting::DESCENDING));

        $slotConfig = $slot->getConfig();
        // Add pagination
        $currentPage = $request->query->getInt('page', 1); // Default to page 1
        $limit = isset($slotConfig["paginationCount"]["value"])?$slotConfig["paginationCount"]["value"]:9;
        $offset = ($currentPage - 1) * $limit;
        $criteria->setLimit($limit);
        $criteria->setOffset($offset);
        $criteriaCollection = new CriteriaCollection();

        // Set total count mode to include the total count
        $criteria->setTotalCountMode(Criteria::TOTAL_COUNT_MODE_EXACT);

        $criteriaCollection->add(
            'gisl_blog_post',
            GislBlogPostDefinition::class,
            $criteria
        );

        return $criteriaCollection;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {

        $gislBlog = $result->get('gisl_blog_post');
        if (!$gislBlog instanceof EntitySearchResult) {
            return;
        }

        $slot->setData($gislBlog);
    }
}