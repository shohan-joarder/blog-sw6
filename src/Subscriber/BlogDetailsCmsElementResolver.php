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


class BlogDetailsCmsElementResolver extends AbstractCmsElementResolver
{
    private EntityRepository $categoryRepository;

    public function __construct(EntityRepository $categoryRepository){
        $this->categoryRepository = $categoryRepository;
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

        if (!$articleId) {
            return null;
        }

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
    
        $title = $firstItemTransData->title ?? '';
        // Prepare the data to be displayed
        $authorName = $blogPost->postAuthor?->name ?? '';
        $authorImage = $blogPost->postAuthor?->media?->url ?? ''; 
        $blogBanner = $blogPost->media?->url ?? '';
        $publishedAt = $blogPost->publishedAt->format('F j, Y') ?? null;
        $blogCategory = $blogPost->categories;
        $catName = implode(",",$categoryNames);
        // dd($title, $description, $tableOfContent, $authorName, $authorImage, $blogBanner, $publishedAt);
        // Create a BlogDetailsDataStruct to store the blog details
        $slotData = new BlogDetailsData($title, $description, $tableOfContent, $authorName, $authorImage, $blogBanner, $publishedAt, $allCategory,$blogCategory,$catName);
    
        $slotData->allCat =$allCategory; 
        $slotData->blogCat =$blogCategory; 
        // Set the data on the slot
        $slot->setData($slotData);
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