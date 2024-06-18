<?php declare(strict_types=1);


namespace Netzkom\OssSelect\Storefront\Controller;

use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\Country\CountryCollection;
use Shopware\Core\System\Country\SalesChannel\AbstractCountryRoute;
use Shopware\Core\System\SalesChannel\SalesChannel\SalesChannelContextSwitcher;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\GenericPageLoaderInterface;
use Shopware\Storefront\Page\Navigation\NavigationPage;
use Shopware\Storefront\Pagelet\Header\HeaderPageletLoaderInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class WidgetController extends StorefrontController
{
    private SystemConfigService $systemConfigService;
    private AbstractCountryRoute $countryRoute;
    private SessionInterface $session;
    private HeaderPageletLoaderInterface $headerLoader;
    private SalesChannelContextSwitcher $contextSwitcher;
    private GenericPageLoaderInterface $genericPageLoader;

    public function __construct(
        SystemConfigService $systemConfigService,
        AbstractCountryRoute $countryRoute,
        SessionInterface $session,
        HeaderPageletLoaderInterface $headerLoader,
        SalesChannelContextSwitcher $contextSwitcher,
        GenericPageLoaderInterface $genericPageLoader
    ) {
        $this->systemConfigService = $systemConfigService;
        $this->countryRoute = $countryRoute;
        $this->session = $session;
        $this->headerLoader = $headerLoader;
        $this->contextSwitcher = $contextSwitcher;
        $this->genericPageLoader = $genericPageLoader;
    }

    /**
     * ...
     *
     * @RouteScope(scopes={"storefront"})
     * @Route("/widgets/netzkom/oss-selector",
     *     name="widgets.netzkom.oss-selector",
     *     options={"seo"="false"},
     *     methods={"GET"}
     * )
     *
     * @param Request $request
     * @param RequestDataBag $data
     * @param Context $context
     * @param SalesChannelContext $salesChannelContext
     *
     * @return Response
     */
    public function index(Request $request, RequestDataBag $data, Context $context, SalesChannelContext $salesChannelContext): Response
    {
        throw new \Exception('not implemented yet');
    }

    /**
     * ...
     *
     * @RouteScope(scopes={"storefront"})
     * @Route("/widgets/netzkom/oss-selector/get-modal",
     *     name="widgets.netzkom.oss-selector.get-modal",
     *     options={"seo"=false},
     *     methods={"GET"},
     *     defaults={"XmlHttpRequest"=true}
     * )
     *
     * @param Request $request
     * @param Context $context
     * @param SalesChannelContext $salesChannelContext
     *
     * @return Response
     */
    public function getModal(Request $request, Context $context, SalesChannelContext $salesChannelContext): Response
    {
        $page = $this->genericPageLoader->load($request, $salesChannelContext);
        $page = NavigationPage::createFrom($page);
        $page->setHeader($this->headerLoader->load($request, $salesChannelContext));
        $page->countries = $this->getCountries($salesChannelContext);

        return $this->renderStorefront('@Storefront/widgets/component/netzkom/oss-selector/modal.html.twig', ['page' => $page]);
    }

    /**
     * ...
     *
     * @param SalesChannelContext $context
     *
     * @return CountryCollection
     */
    private function getCountries(SalesChannelContext $context): CountryCollection
    {
        $countries = $this->countryRoute->load(new Request(), new Criteria(), $context)->getCountries();
        $countries->sortByPositionAndName();
        return $countries;
    }

    /**
     * ...
     *
     * @RouteScope(scopes={"storefront"})
     * @Route("/widgets/netzkom/oss-selector/get-status",
     *     name="widgets.netzkom.oss-selector.get-status",
     *     options={"seo"=false},
     *     methods={"GET"},
     *     defaults={"XmlHttpRequest"=true}
     * )
     *
     * @param Request $request
     * @param Context $context
     * @param SalesChannelContext $salesChannelContext
     *
     * @return Response
     */
    public function getStatus(Request $request, Context $context, SalesChannelContext $salesChannelContext): JsonResponse
    {
        // always disable when disabled in configuration
        if ($this->systemConfigService->get('NetzkomOssSelect.config.status', $salesChannelContext->getSalesChannelId()) !== true) {
            // instant return
            return new JsonResponse(['success' => true, 'open' => false]);
        }

        // get current status
        $open = !$this->session->has('netzkom-oss-selector');

        // not yet set and we only want to show it one time?
        if ($open === true && $this->systemConfigService->get('NetzkomOssSelect.config.forceSelection', $salesChannelContext->getSalesChannelId()) === false) {
            // set as already set
            $this->session->set('netzkom-oss-selector', true);
        }

        // return as json
        return new JsonResponse(['success' => true, 'open' => $open]);
    }

    /**
     * ...
     *
     * @RouteScope(scopes={"storefront"})
     * @Route("/widgets/netzkom/oss-selector/set-context",
     *     name="widgets.netzkom.oss-selector.set-context",
     *     options={"seo"=false},
     *     methods={"POST"},
     *     defaults={"XmlHttpRequest"=true}
     * )
     *
     * @param Request $request
     * @param RequestDataBag $data
     * @param Context $context
     * @param SalesChannelContext $salesChannelContext
     *
     * @return JsonResponse
     */
    public function setContext(Request $request, RequestDataBag $data, Context $context, SalesChannelContext $salesChannelContext): JsonResponse
    {
        $this->contextSwitcher->update($data, $salesChannelContext);
        $this->session->set('netzkom-oss-selector', true);
        if (!empty($data->get('languageId'))) {
            return new JsonResponse([
                'success' => true,
                'languageId' => $data->get('languageId')
            ]);
        }

        return new JsonResponse(['success' => true]);
    }
}
