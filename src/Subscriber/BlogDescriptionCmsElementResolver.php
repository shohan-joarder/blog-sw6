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
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Gisl\GislBlog\Subscriber\Struct\BlogDescriptionData;


class BlogDescriptionCmsElementResolver extends AbstractCmsElementResolver
{
    private SystemConfigService $systemConfigService;
    private EntityRepository $mediaRepository;

    public function __construct(SystemConfigService $systemConfigService,EntityRepository $mediaRepository)
    {
        $this->systemConfigService = $systemConfigService;
        $this->mediaRepository = $mediaRepository;
    }

    public function getType(): string
    {
        return 'gisl-blog-description';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        // Retrieve plugin configuration
        $pluginConfig = $this->systemConfigService->get('GislBlog.config');
        $title = $pluginConfig['title'] ?? null;
        $description = $pluginConfig['description'] ?? null;

        // dd(new BlogShortDescriptionData);

        // Create the custom Struct object with the data
        $data = new BlogDescriptionData($title, $description);

        // Set the data on the slot
        $slot->setData($data);


        return null; // No CriteriaCollection needed here, just set data
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {

    }
}