<?php
/**
 * Shopware 4
 * Copyright © shopware AG
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

namespace Shopware\Bundle\SearchBundle\DBAL\ConditionHandler;

use Shopware\Bundle\SearchBundle\Condition\ImmediateDeliveryCondition;
use Shopware\Bundle\SearchBundle\ConditionInterface;
use Shopware\Bundle\SearchBundle\DBAL\ConditionHandlerInterface;
use Shopware\Bundle\StoreFrontBundle\Struct\ShopContextInterface;
use Shopware\Bundle\SearchBundle\DBAL\QueryBuilder;

/**
 * @category  Shopware
 * @package   Shopware\Bundle\SearchBundle\DBAL\ConditionHandler
 * @copyright Copyright (c) shopware AG (http://www.shopware.de)
 */
class ImmediateDeliveryConditionHandler implements ConditionHandlerInterface
{
    /**
     * {@inheritdoc}
     */
    public function supportsCondition(ConditionInterface $condition)
    {
        return ($condition instanceof ImmediateDeliveryCondition);
    }

    /**
     * Handles the passed condition object.
     * Extends the provided query builder with the specify conditions.
     * Should use the andWhere function, otherwise other conditions would be overwritten.
     *
     * @param ConditionInterface $condition
     * @param QueryBuilder $query
     * @param ShopContextInterface $context
     * @return void
     */
    public function generateCondition(
        ConditionInterface $condition,
        QueryBuilder $query,
        ShopContextInterface $context
    ) {

        $subSquery = <<<EOT
(
    SELECT count(id)
    FROM s_articles_details
    WHERE s_articles_details.articleID = product.id
    AND s_articles_details.active = 1
    AND s_articles_details.instock >= s_articles_details.minpurchase
) >= 1
EOT;

        $query->andWhere($subSquery);
    }
}