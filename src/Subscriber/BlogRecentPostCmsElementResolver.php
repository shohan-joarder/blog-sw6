<?php declare(strict_types=1);

namespace Gisl\GislBlog\Subscriber;

use Shopware\Core\Framework\Context;
use Gisl\GislBlog\Subscriber\Struct\BlogRelatedPostData;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Gisl\GislBlog\Core\Content\GislBlogPost\GislBlogPostDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;


class BlogRecentPostCmsElementResolver extends AbstractCmsElementResolver
{

    public function getType(): string
    {
        return 'gisl-recent-blog';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        $criteria = new Criteria();
    
        $dateTime = new \DateTime();
    
        // Add filters for blogs
        $criteria->addFilter(
            new EqualsFilter('translations.type', 'blog'),
            new EqualsFilter('active', true),
            new RangeFilter('publishedAt', [RangeFilter::LTE => $dateTime->format(\DATE_ATOM)])
        );
    
        // Add associations to load related data
        $criteria->addAssociations([
            'translations',
            'postAuthor.media',
            'media'
        ]);
    
        // Order by `publishedAt` descending to get the latest posts first
        $criteria->addSorting(new FieldSorting('publishedAt', FieldSorting::DESCENDING));
    
        // Limit to 3 results
        $criteria->setLimit(3);
    
        // Set total count mode to include the total count
        $criteria->setTotalCountMode(Criteria::TOTAL_COUNT_MODE_EXACT);
    
        // Add criteria to the collection
        $criteriaCollection = new CriteriaCollection();
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