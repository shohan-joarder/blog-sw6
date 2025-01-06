<?php declare(strict_types=1);

namespace Gisl\GislBlog\Subscriber;

use Shopware\Core\Framework\Context;
use Gisl\GislBlog\Subscriber\Struct\BlogRelatedPostData;
use Shopware\Core\Content\Cms\Aggregate\CmsSlot\CmsSlotEntity;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Content\Cms\DataResolver\Element\ElementDataCollection;
use Shopware\Core\Content\Cms\DataResolver\ResolverContext\ResolverContext;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Content\Cms\DataResolver\Element\AbstractCmsElementResolver;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;

// :src="assetFilter('/administration/static/img/cms/preview/blog_list.png')"
class BlogRelatedPostCmsElementResolver extends AbstractCmsElementResolver
{
    private EntityRepository $blogRepository;

    public function __construct(EntityRepository $blogRepository){
        $this->blogRepository = $blogRepository;
    }

    public function getType(): string
    {
        return 'gisl-related-blog';
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

        $findBlogCriteria =  new Criteria();

        $findBlogCriteria->addFilter(
            new EqualsFilter('id', $articleId),
            new EqualsFilter('active', true)
        );

        // Fetch category collection from the repository
        $blogCollection = $this->blogRepository->search($findBlogCriteria, Context::createDefaultContext());

        // Retrieve the category entities
        $blog = $blogCollection->getEntities()->first();
        
        if(!$blog){
            return null;
        }

        $categories = $blog->categories;

        if(count($categories) > 0){

            $criteria = new Criteria();

            // Add filter to check if the JSON field `categories` contains any of the given category IDs
            foreach ($categories as $categoryId) {
                // Add a filter to exclude the specific blog ID
                // $criteria->addFilter(new NotEqualsFilter('id', $articleId));
                // Add a filter to exclude the specific blog ID
                $criteria->addFilter(
                    new NotFilter(
                        NotFilter::CONNECTION_AND,
                        [new EqualsFilter('id', $articleId)]
                    )
                );
                $criteria->addFilter(
                    new ContainsFilter('categories', $categoryId),
                    new EqualsFilter('active', true),
                    // new NotEqualsFilter('id', $articleId)
                );
            }
            // Add associations to load related data
            $criteria->addAssociations([
                'translations',
                'postAuthor.media',
                'media'
            ]);
            
            $criteria->setLimit(6);

            // Execute the query
            $relatedBlogs = 
            
                $this->blogRepository->search($criteria, Context::createDefaultContext());
            
            // Create the custom Struct object with the data
            $data = new BlogRelatedPostData($relatedBlogs);

            // Set the data on the slot
            $slot->setData($data);

            // dd($relatedBlogs);


        }else{
            return null;
        }

        return null;
    }

    public function enrich(CmsSlotEntity $slot, ResolverContext $resolverContext, ElementDataCollection $result): void
    {

    }
}