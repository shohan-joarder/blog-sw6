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
use Gisl\GislBlog\Subscriber\Struct\BlogShortDescriptionData;
use Shopware\Core\Framework\Context;

class BlogShortDescriptionCmsElementResolver extends AbstractCmsElementResolver
{
    private SystemConfigService $systemConfigService;
    private EntityRepository $mediaRepository;
    private EntityRepository $categoryRepository;

    public function __construct(SystemConfigService $systemConfigService,EntityRepository $mediaRepository,EntityRepository $categoryRepository)
    {
        $this->systemConfigService = $systemConfigService;
        $this->mediaRepository = $mediaRepository;
        $this->categoryRepository = $categoryRepository;
    }

    public function getType(): string
    {
        return 'gisl-blog-short-description';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {

        $request = $resolverContext->getRequest();

        $categoryId = $request->query->get("category");

        $banner = "";
        $shortDescription="";
        $title = "";

        if(!$categoryId){

            // Retrieve plugin configuration
            $pluginConfig = $this->systemConfigService->get('GislBlog.config');

            $mediaId = $pluginConfig['Banner'] ?? null;
            $shortDescription = $pluginConfig['shortDescription'] ?? null;

            $banner = null;

            if ($mediaId) {
                // Define criteria to fetch media by ID
                $mediaCriteria = new Criteria([$mediaId]);

                // Get the context from ResolverContext
                $context = $resolverContext->getSalesChannelContext()->getContext();

                // Search for the media entity
                $media = $this->mediaRepository->search($mediaCriteria, $context)->first();

                if ($media) {
                    $banner = $media->getUrl(); // Get the media URL
                }
            }

        }else{

            // get categories
            $categoryCriteria = new Criteria();

            $categoryCriteria->addFilter(
                new EqualsFilter('id', $categoryId)
            );

            $categoryCriteria->addAssociations([
                'translations',
                'media'
            ]);
        
            // Fetch category collection from the repository
            $categoryCollection = $this->categoryRepository->search($categoryCriteria, Context::createDefaultContext());
        
            // Retrieve the category entities
            $categories = $categoryCollection->getEntities()->first();

            if($categories){

                if($categories){
                    $media = $categories?->media;
                }

                $firstItem = $categories->translations->first();

                $shortDescription = $firstItem->short_description;
                $title = $firstItem->title;
            }
            
        }

        // dd(new BlogShortDescriptionData);

        // Create the custom Struct object with the data
        $data = new BlogShortDescriptionData($banner, $shortDescription);

        $data->media = $media ?? null;
        $data->title = $title;

        // Set the data on the slot
        $slot->setData($data);


        return null; // No CriteriaCollection needed here, just set data
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {

    }
}