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
use Shopware\Core\System\SystemConfig\SystemConfigService;

class BlogCategoryCmsElementResolver extends AbstractCmsElementResolver
{
    private SystemConfigService $systemConfigService;

    public function __construct(SystemConfigService $systemConfigService)
    {
        $this->systemConfigService = $systemConfigService;
    }

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
        $request = $resolverContext->getRequest();

        $categoryId = $request->query->get("category");

        $pluginConfig = $this->systemConfigService->get('GislBlog.config');

        $listingUrl = $pluginConfig['blogListingUrl'] ?? "blog";

        $gislBlog = $result->get('gisl_blog_category');

        if (!$gislBlog instanceof EntitySearchResult) {
            return;
        }

        $gislBlog->blogListingUrl = $listingUrl;

        $gislBlog->categoryId = $categoryId??null;

        $slot->setData($gislBlog);
    }
}