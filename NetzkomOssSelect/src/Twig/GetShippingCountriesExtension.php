<?php declare(strict_types=1);

namespace Netzkom\OssSelect\Twig;

use Shopware\Core\Framework\Context;
use Shopware\Core\System\Country\CountryCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\System\Country\SalesChannel\AbstractCountryRoute;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Symfony\Component\HttpFoundation\Request;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GetShippingCountriesExtension extends AbstractExtension
{

    private AbstractCountryRoute $countryRoute;

    public function __construct(
        AbstractCountryRoute $countryRoute
    ) {
        $this->countryRoute = $countryRoute;
    }
    public function getFunctions(): array
    {
        return [
            new TwigFunction('getShippingCountries', [$this, 'getShippingCountries']),
        ];
    }

    public function getShippingCountries(SalesChannelContext $context): CountryCollection
    {
        $countries = $this->countryRoute->load(new Request(), new Criteria(), $context)->getCountries();
        $countries->sortByPositionAndName();
        return $countries;

    }
}