<?php
namespace Gisl\GislBlog\Storefront\Controller;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoader;
use Shopware\Core\Content\Cms\SalesChannel\SalesChannelCmsPageLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\ContainsFilter;
use Shopware\Core\Framework\Context;
use Shopware\Core\Content\Cms\DataResolver\CriteriaCollection;
use Gisl\GislBlog\Core\Content\GislBlogPost\GislBlogPostDefinition;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class GislBlogController extends StorefrontController
{
    private SystemConfigService $systemConfigService;
    private SalesChannelCmsPageLoader $cmsPageLoader;  // Correct service for CMS page loading
    private GenericPageLoader $genericPageLoader;
    private EntityRepository $transRepository;
    private EntityRepository $blogPostRepository;
    // Inject the correct services
    public function __construct(
        SystemConfigService $systemConfigService,
        SalesChannelCmsPageLoader $cmsPageLoader,  // Ensure the correct class is injected
        GenericPageLoader $genericPageLoader,
        EntityRepository $transRepository,
        EntityRepository $blogPostRepository
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->cmsPageLoader = $cmsPageLoader;
        $this->genericPageLoader = $genericPageLoader;
        $this->transRepository = $transRepository;
        $this->blogPostRepository = $blogPostRepository;
    }


    #[Route(path: '/gisl-blog/{slug}', name: 'gisl.frontend.blog.detail', methods: ['GET'])]
    public function detailAction(string $slug,Request $request, SalesChannelContext $context): Response
    {
            // Create criteria for querying blog by slug
        $transCriteria = new Criteria();
        $transCriteria->addFilter(new EqualsFilter('slug', $slug));

        // Fetch the blog data from the repository
        $transCollection = $this->transRepository->search($transCriteria, $context->getContext());
        $transData = $transCollection->first();

        // Check if the blog with the given slug exists
        if (!$transData) {
            throw new \RuntimeException('Invalid slug.');
        }

        // Load generic page (header, footer, etc.)
        $pageInfo = $this->genericPageLoader->load($request, $context);

        // dd($transData);
        // Set meta information
        $meta = $pageInfo->getMetaInformation();
        $transData->metaTitle ? $meta->setMetaTitle($transData->metaTitle) : "";
        $transData->metaDescription ? $meta->setMetaDescription($transData->metaDescription):"";
        $transData->metaKeywords ? $meta->setMetaKeywords($transData->metaKeywords) : "";

        // Retrieve the plugin configuration
        $pluginConfig = $this->systemConfigService->get('GislBlog.config');
    
        // Ensure that the CMS Blog Detail Page ID is configured
        if (empty($pluginConfig['cmsBlogDetailPage'])) {
            throw new \RuntimeException('CMS Blog Detail Page ID is not configured.');
        }
    
        $cmsPageId = $pluginConfig['cmsBlogDetailPage'];
    
        // Create a Criteria object to find the CMS page
        $criteria = new Criteria();
        $criteria->setIds([$cmsPageId]);
    
        // Load the CMS page using the SalesChannelCmsPageLoader
        $cmsPageResult = $this->cmsPageLoader->load($request, $criteria, $context);
    
        // Ensure that the CMS page result is not empty
        $cmsPage = $cmsPageResult->first();
        if (!$cmsPage) {
            throw new \RuntimeException('CMS page not found.');
        }
    
        // Set the CMS page to the page object
        // Load the generic page (header, footer, etc.)

        $pageInfo->cmsPage = $cmsPage;

        // Render the CMS page with the template
        return $this->renderStorefront('@Storefront/storefront/page/content/index.html.twig', [
            'page' => $pageInfo,
            'slug' => $slug
        ]);
    }

    #[Route(path: '/gisl-blog-search', name: 'gisl.blog.search', methods: ['POST'])]
    public function searchBlog(Request $request): Response
    {
        // Retrieve search term from request
        $searchTerm = $request->request->get('searchTerm');

        // Check if the search term is provided
        if (empty($searchTerm)) {
            return new Response(json_encode(['count' => 0]), Response::HTTP_OK, ['Content-Type' => 'application/json']);
        }

        // Create criteria for searching blog posts
        $criteria = new Criteria();

        // Get the current date
        $currentDate = (new \DateTime())->format('Y-m-d H:i:s');

        // Create a range filter for publishedAt
        $rangeFilter = new RangeFilter('publishedAt', [
            'lte' => $currentDate // 'lte' means less than or equal to
        ]);

        $criteria->addFilter(new ContainsFilter('title', $searchTerm));
        $criteria->addFilter(new ContainsFilter('type', "blog"));

        // Apply pagination (page number and limit per page)
        $page = $request->query->getInt('page', 1); // Default to page 1
        $limit = $request->query->getInt('limit', 3); // Default to 2 items per page

        // Set pagination criteria
        $criteria->setLimit($limit);
        $criteria->setOffset(($page - 1) * $limit);

        // Search for matching blog posts
        $context = Context::createDefaultContext();
        $result = $this->transRepository->search($criteria, $context);

        // Clone criteria for total count query (exclude limit and offset)
        $totalCriteria = clone $criteria;
        $totalCriteria->setLimit(null);
        $totalCriteria->setOffset(null);

        // Retrieve total count of results
        $totalCount = $this->transRepository->search($totalCriteria, $context)->getTotal();

        $items =  $result->getEntities()->getElements();
        // dd($result->getEntities()->getElements());
        // Get the count of matching blog posts
        $htmlRes = $this->renderView('@Storefront/storefront/inc/blog-search-result.html.twig', [
            'items' => $result->getEntities()->getElements(),
            'count' => $totalCount,
            'searchTerm'=>$searchTerm
        ]);
        
        // Return the count as a JSON response
        return new Response(json_encode(['count' => $totalCount,'html_res'=> $htmlRes]), Response::HTTP_OK, ['Content-Type' => 'application/json']);
        
    }

    #[Route(path: '/gisl-blog-search', name: 'gisl.frontend.blog.search', methods: ['GET'])]
    public function actionSearchBlog(Request $request,SalesChannelContext $context): Response
    {
        $pageInfo =  $this->genericPageLoader->load($request, $context);

        $searchTerm = $request->get('sq');
        // Apply pagination (page number and limit per page)
        $page = $request->query->getInt('p', 1); // Default to page 1
        $limit = $request->query->getInt('limit', 9); // Default to 2 items per page

        // Check if the search term is provided
        if (empty($searchTerm)) {
            return new Response(json_encode(['count' => 0]), Response::HTTP_OK, ['Content-Type' => 'application/json']);
        }

        // Initialize criteria
        $criteria = new Criteria();

        // Set pagination criteria
        $criteria->setLimit($limit);
        $criteria->setOffset(($page - 1) * $limit);

        // Get current date and time
        $dateTime = new \DateTime();

        // Add filters to the criteria
        $criteria->addFilter(
            new EqualsFilter('translations.type', 'blog'), // Filter by blog type
            new EqualsFilter('active', true), // Only active blogs
            new RangeFilter('publishedAt', [RangeFilter::LTE => $dateTime->format(\DATE_ATOM)]) // Published before now
        );

        $criteria->addFilter(new ContainsFilter('translations.title', $searchTerm));

        // Add necessary associations
        $criteria->addAssociations([
            'translations',
            'postAuthor.media',
            'media'
        ]);

        // Set total count mode to include the exact total count
        $criteria->setTotalCountMode(Criteria::TOTAL_COUNT_MODE_EXACT);

        // Execute the query using the repository
        $result = $this->blogPostRepository->search($criteria, $context->getContext());

        // Get the total count and items
        $totalCount = $result->getTotal();
        $items = $result->getEntities()->getElements();

        // dd($totalCount);

        return $this->renderStorefront('@Storefront/storefront/page/blog-search.html.twig', [
            'page' => $pageInfo,
            'items' => $items,
            'totalCount'=>$totalCount,
            'searchTerm'=>$searchTerm,
            'totalPages'=>ceil($totalCount / $limit)
        ]);

    }

}

