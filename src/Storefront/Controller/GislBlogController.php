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

#[Route(defaults: ['_routeScope' => ['storefront']])]
class GislBlogController extends StorefrontController
{
    private SystemConfigService $systemConfigService;
    private SalesChannelCmsPageLoader $cmsPageLoader;  // Correct service for CMS page loading
    private GenericPageLoader $genericPageLoader;
    private EntityRepository $transRepository;
    // Inject the correct services
    public function __construct(
        SystemConfigService $systemConfigService,
        SalesChannelCmsPageLoader $cmsPageLoader,  // Ensure the correct class is injected
        GenericPageLoader $genericPageLoader,
        EntityRepository $transRepository
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->cmsPageLoader = $cmsPageLoader;
        $this->genericPageLoader = $genericPageLoader;
        $this->transRepository = $transRepository;
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
        $limit = $request->query->getInt('limit', 10); // Default to 2 items per page

        // Set pagination criteria
        $criteria->setLimit($limit);
        $criteria->setOffset(($page - 1) * $limit);

        // Search for matching blog posts
        $context = Context::createDefaultContext();
        $result = $this->transRepository->search($criteria, $context);

        // Get total count of matching blog posts
        $totalResults = $result->getTotal();
        
        // Get the count of matching blog posts
        // $count = $result->getTotal();

        // Prepare response data
        $responseData = [
            'count' => $totalResults, // Total count of results
            'currentPage' => $page,
            'limit' => $limit,
            'data' => $result->getEntities()->getElements() // Paginated blog post data
        ];

        dd($responseData);
        
        // Return the count as a JSON response
        return new Response(json_encode(['count' => $count,'sarch_url'=>  $this->router->generate('frontend.blog', [
            'search' => $searchTerm  // Assuming the `slug` is the correct parameter
        ]),]), Response::HTTP_OK, ['Content-Type' => 'application/json']);
        
    }

}