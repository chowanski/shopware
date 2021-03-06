<?php
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Bundle\PluginInstallerBundle\Service;

use Shopware\Bundle\PluginInstallerBundle\Context\LicenceRequest;
use Shopware\Bundle\PluginInstallerBundle\Context\ListingRequest;
use Shopware\Bundle\PluginInstallerBundle\Context\PluginLicenceRequest;
use Shopware\Bundle\PluginInstallerBundle\Context\PluginsByTechnicalNameRequest;
use Shopware\Bundle\PluginInstallerBundle\Context\UpdateListingRequest;
use Shopware\Bundle\PluginInstallerBundle\StoreClient;
use Shopware\Bundle\PluginInstallerBundle\Struct\CategoryStruct;
use Shopware\Bundle\PluginInstallerBundle\Struct\LicenceStruct;
use Shopware\Bundle\PluginInstallerBundle\Struct\ListingResultStruct;
use Shopware\Bundle\PluginInstallerBundle\Struct\PluginStruct;
use Shopware\Bundle\PluginInstallerBundle\Struct\StructHydrator;

/**
 * Class PluginStoreService
 * @package Shopware\Bundle\PluginInstallerBundle\Service
 */
class PluginStoreService
{
    /**
     * @var StoreClient
     */
    private $storeClient;

    /**
     * @var StructHydrator
     */
    private $hydrator;

    /**
     * @param StoreClient $storeClient
     * @param StructHydrator $hydrator
     */
    public function __construct(
        StoreClient $storeClient,
        StructHydrator $hydrator
    ) {
        $this->storeClient = $storeClient;
        $this->hydrator = $hydrator;
    }

    /**
     * @param ListingRequest $context
     * @return ListingResultStruct
     * @throws \Exception
     */
    public function getListing(ListingRequest $context)
    {
        $params = [
            'locale'          => $context->getLocale(),
            'shopwareVersion' => $context->getShopwareVersion(),
            'offset'          => $context->getOffset(),
            'limit'           => $context->getLimit(),
            'sort'            => json_encode($context->getSortings()),
            'filter'          => json_encode($context->getConditions()),
        ];

        $data = $this->storeClient->doGetRequest(
            '/pluginStore/plugins',
            $params
        );

        $plugins = $this->hydrator->hydrateStorePlugins($data['data']);

        return new ListingResultStruct(
            $plugins,
            $data['total']
        );
    }

    /**
     * @param PluginsByTechnicalNameRequest $context
     * @return PluginStruct
     */
    public function getPlugin(PluginsByTechnicalNameRequest $context)
    {
        $plugins = $this->getPlugins($context);

        return array_shift($plugins);
    }

    /**
     * @param PluginsByTechnicalNameRequest $context
     * @return PluginStruct[]
     */
    public function getPlugins(PluginsByTechnicalNameRequest $context)
    {
        $params = [
            'locale'          => $context->getLocale(),
            'shopwareVersion' => $context->getShopwareVersion(),
            'technicalNames'  => $context->getTechnicalNames()
        ];

        $data = $this->storeClient->doGetRequest(
            '/pluginStore/pluginsByName',
            $params
        );

        return $this->hydrator->hydrateStorePlugins($data);
    }


    /**
     * @param UpdateListingRequest $context
     * @return PluginStruct[]
     * @throws \Exception
     */
    public function getUpdates(UpdateListingRequest $context)
    {
        $result = $this->storeClient->doGetRequest(
            '/pluginStore/updateablePlugins',
            [
                'shopwareVersion' => $context->getShopwareVersion(),
                'domain' => $context->getDomain(),
                'locale' => $context->getLocale(),
                'plugins' => $context->getPlugins()
            ]
        );
        
        $plugins = $this->hydrator->hydrateStorePlugins($result['data']);

        return $plugins;
    }

    /**
     * @param PluginLicenceRequest $context
     * @return LicenceStruct
     */
    public function getPluginLicence(PluginLicenceRequest $context)
    {
        $content = $this->storeClient->doAuthGetRequest(
            $context->getToken(),
            '/licenses',
            [
                'shopwareVersion' => $context->getShopwareVersion(),
                'domain' => $context->getDomain(),
                'pluginName' => $context->getTechnicalName(),
            ]
        );

        $licence = $this->hydrator->hydrateLicences($content);

        return array_shift($licence);
    }

    /**
     * @param LicenceRequest $context
     * @return array
     * @throws \Exception
     */
    public function getLicences(
        LicenceRequest $context
    ) {
        $result = $this->storeClient->doAuthGetRequest(
            $context->getToken(),
            '/licenses',
            [
                'shopwareVersion' => $context->getShopwareVersion(),
                'domain' => $context->getDomain()
            ]
        );

        return $this->hydrator->hydrateLicences($result);
    }

    /**
     * @return CategoryStruct[]
     * @throws \Exception
     */
    public function getCategories()
    {
        $data = $this->storeClient->doGetRequest(
            '/pluginStore/categories'
        );

        return $this->hydrator->hydrateCategories($data);
    }
}
