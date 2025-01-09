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
use Gisl\GislBlog\Core\Content\GislBlogCategory\GislBlogCategoryDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Symfony\Component\HttpFoundation\Request;
use Shopware\Core\Defaults;
use Gisl\GislBlog\Subscriber\Struct\BlogDetailsData;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use DOMDocument;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SystemConfig\SystemConfigService;



class BlogDetailsCmsElementResolver extends AbstractCmsElementResolver
{
    private EntityRepository $categoryRepository;
    private SystemConfigService $systemConfigService;
    private EntityRepository $productRepository;
    private EntityRepository $transRepository;
    public function __construct(EntityRepository $categoryRepository,SystemConfigService $systemConfigService,EntityRepository $productRepository,EntityRepository $transRepository){
        $this->categoryRepository = $categoryRepository;
        $this->systemConfigService = $systemConfigService;
        $this->productRepository = $productRepository;
        $this->transRepository = $transRepository;
    }

    public function getType(): string
    {
        return 'gisl-blog-details';
    }

    public function collect(CmsSlotEntity $slot, ResolverContext $resolverContext): ?CriteriaCollection
    {
        // Collect criteria logic remains unchanged
        $request = $resolverContext->getRequest();

        if (!$request instanceof \Symfony\Component\HttpFoundation\Request) {
            return null;
        }

        $articleId = $request->attributes->get('articleId');

        $slug = $request->attributes->get("slug");

        if (!$slug) {
            return null;
        }

        // Create criteria for querying products
        $transCriteria = new Criteria();

        $transCriteria->addFilter(new EqualsFilter('slug', $slug));

        // Fetch trans collection from the repository
        $transCollection = $this->transRepository->search($transCriteria, Context::createDefaultContext());

        // Retrieve the trans entities
        $transData = $transCollection->getEntities()->first();

        if(!$transData){
            return null;
        }

        $articleId = $transData->fkId;

        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('id', $articleId),
            new EqualsFilter('active', true)
        );

        $criteria->addAssociations([
            'translations',
            'postAuthor.media',
            'media'
        ]);

        $criteriaCollection = new CriteriaCollection();
        
        $criteriaCollection->add('gisl_blog_post', GislBlogPostDefinition::class, $criteria);

        return $criteriaCollection;
    }

    public function enrich(
        CmsSlotEntity $slot,
        ResolverContext $resolverContext,
        ElementDataCollection $result
    ): void {

        $pluginConfig = $this->systemConfigService->get('GislBlog.config');

        $listingUrl = $pluginConfig['blogListingUrl'] ?? "blog";

        $salesChannelContext = $resolverContext->getSalesChannelContext();
    
        // Get the current language ID from the SalesChannelContext
        $langId = $salesChannelContext->getContext()->getLanguageId();

        // Retrieve the result for 'gisl_blog_post'
        $blogPostResult = $result->get('gisl_blog_post');

        if (!$blogPostResult instanceof EntitySearchResult || $blogPostResult->count() === 0) {
            $slot->setData(null); // No blog post found
            return;
        }
    
        // Get the first blog post
        $blogPost = $blogPostResult->first();

        // get categories
        $categoryCriteria = new Criteria();

        $categoryCriteria->addFilter(
            new EqualsFilter('active', true)
        );

        $categoryCriteria->addAssociations([
            'translations'
        ]);
    
        // Fetch category collection from the repository
        $categoryCollection = $this->categoryRepository->search($categoryCriteria, Context::createDefaultContext());
    
        // Retrieve the category entities
        $categories = $categoryCollection->getEntities();

        $allCategory = [];

        $categoryNames = []; 

        if(count($categories) > 0){
            foreach ($categories as $key => $category) {
               
                $catTransData = $category->translations;
                if(count($catTransData) > 1){
                    foreach ($catTransData as $key => $catTrans) {
                        if ($catTrans->languageId === $langId) {
                            $allCategory[$category->id] = $category;
                            if(in_array($category->id,$blogPost->categories)){
                                $categoryNames[] = $catTrans->title;
                            }
                            break;
                        }
                    }
                }else{
                    $firstItem = $catTransData->first();
                    if(in_array($category->id,$blogPost->categories)){
                        $categoryNames[] = $firstItem->title;
                    }
                    $allCategory[$category->id] = $firstItem;
                }
            }
        }

        // Retrieve the category IDs
        $categoryIds = $blogPost->categories;
    
        // Retrieve translations
        $transData = $blogPost->translations;
    
        // Default to the first translation if no specific language match is found
        $firstItemTransData = $transData->first();
    
        // Iterate to find the translation for the current language
        foreach ($transData as $translation) {
            if ($translation->languageId === $langId) {
                $firstItemTransData = $translation;
                break;
            }
        }
    
        $description = $firstItemTransData->description ?? ''; // Ensure description is never null
    
        // Replace all occurrences of '%5BCLONE%5D' with ':', etc.
        if (!empty($description)) {
            $description = str_replace(
                ['[CLONE]', '[SEMICLONE]', '[COMMA]', '%5BCLONE%5D', '%5BSEMICLONE%5D', '%5BCOMMA%5D', '[iframe]', '&gt;[/iframe]'],
                [':', ';', ',', ':', ';', ',', '<iframe', '></iframe>'],
                $description
            );
            $tableOfContentWithDesc = $this->makeToc($description);

            $description = $tableOfContentWithDesc["desc"];
            $tableOfContent = $tableOfContentWithDesc["toc"];
        } else {
            $tableOfContent = ""; // Set a default value if no description is available
        }
    
        $relatedProduct = [];

        if($blogPost->tags){

            $relatedProduct = $this->makeRelatedProduct($blogPost->tags,$salesChannelContext);
        }
        

        $title = $firstItemTransData->title ?? '';
        // Prepare the data to be displayed
        $authorName = $blogPost->postAuthor?->name ?? '';
        $authorImage = $blogPost->postAuthor?->media?->url ?? ''; 
        $blogBanner = $blogPost->media?->url ?? '';
        $publishedAt = $blogPost->publishedAt->format('F j, Y') ?? null;
        $blogCategory = $blogPost->categories;
        $catName = implode(",",$categoryNames);
        // Create a BlogDetailsDataStruct to store the blog details
        $slotData = new BlogDetailsData($title, $description, $tableOfContent, $authorName, $authorImage, $blogBanner, $publishedAt, $allCategory,$blogCategory,$catName);
    
        $slotData->allCat =$allCategory; 
        $slotData->blogCat =$blogCategory; 
        $slotData->blogListingUrl = $listingUrl;
        $slotData->relatedProduct = $relatedProduct;
        $slotData->media = $blogPost?->media??null;

        // Set the data on the slot
        $slot->setData($slotData);
    }
    
    private function makeRelatedProduct(array $tagIds, $salesChannelContext)
    {
        try {
            // Retrieve the current context from the SalesChannelContext
            $context = $salesChannelContext->getContext();
    
            // Create criteria for querying products
            $criteria = new Criteria();
            // Set a limit of 5 results
            $criteria->setLimit(5);
            // Load necessary associations
            $criteria->addAssociation('tags');
            $criteria->addAssociation('seoUrls');
            $criteria->addAssociation('media');
            $criteria->addAssociation('cover');
            $criteria->addAssociation('price');
            $criteria->addAssociation('priceDetails');
    
            // Filters to include only relevant products
            $criteria->addFilter(new RangeFilter('stock', ['gt' => 0])); // Only products with stock > 0
            $criteria->addFilter(new EqualsFilter('available', true));   // Only available products
            $criteria->addFilter(new EqualsFilter('active', true));      // Only active products
    
            // Filter by tag IDs (support for multiple tags using MultiFilter)
            if (!empty($tagIds)) {
                $criteria->addFilter(new MultiFilter(
                    MultiFilter::CONNECTION_OR,
                    array_map(fn($tagId) => new EqualsFilter('tags.id', $tagId), $tagIds)
                ));
            }
    
            // Add filter to include only parent products or one variant (if variants exist)
            $criteria->addFilter(new MultiFilter(
                MultiFilter::CONNECTION_OR, [
                    new EqualsFilter('parentId', null),      // Parent products
                    new EqualsFilter('displayGroup', 'variant') // One variant if present
                ]
            ));
    
            // Perform the search in the repository
           return $productEntities = $this->productRepository->search($criteria, $context)->getEntities();

            $rp;

            if(count($productEntities)>0){
                foreach ($productEntities as $key => $product) {
                    dd($product->getPrice);
                    $productPrice = $product->price->first();
                    // dd($productPrice);
                     // Add calculated prices to product
                    $product->calculatedPrice = $productPrice;
                }
            }

            // dd($rp);
            // Return the resulting product entities
            return $rp;
        } catch (\Exception $e) {
            // Log the error for debugging
            $this->logger->error('Error in makeRelatedProduct function: ' . $e->getMessage(), [
                'exception' => $e,
                'tagIds' => $tagIds
            ]);
    
            // Return an empty result in case of an exception
            return [];
        }
    }

    private function makeToc($content)
    {
        $dom = new DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $content);
        $toc = [];
    
        // Process h2 and h3 tags for the TOC
        foreach (['h2', 'h3'] as $tag) {
            $elements = $dom->getElementsByTagName($tag);
            foreach ($elements as $element) {
                $textContent = $element->textContent;
                $id = $element->getAttribute('id') ?: str_replace(' ', '-', strtolower($textContent));
    
                // Add id to the element if it doesn't have one
                if (!$element->getAttribute('id')) {
                    $element->setAttribute('id', $id);
                }
    
                // Add this heading to the TOC array
                $toc[] = [
                    'level' => $tag,
                    'text' => $textContent,
                    'id' => $id
                ];
            }
        }
    
        // Generate the TOC HTML
        $tocHtml = '';
        foreach ($toc as $heading) {
            $tocHtml .= '<li class="toc-' . $heading['level'] . '">';
            $tocHtml .= '<a href="#' . $heading['id'] . '">' . $heading['text'] . '</a>';
            $tocHtml .= '</li>';
        }
        $modifiedContent = $dom->saveHTML($dom->getElementsByTagName('body')->item(0));
        return ['toc'=>$tocHtml,'desc'=>$modifiedContent];
    }
    
}