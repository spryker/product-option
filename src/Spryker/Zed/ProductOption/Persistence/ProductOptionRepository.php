<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Persistence;

use Generated\Shared\Transfer\ProductAbstractOptionGroupStatusTransfer;
use Generated\Shared\Transfer\ProductOptionCriteriaTransfer;
use Orm\Zed\ProductOption\Persistence\Map\SpyProductAbstractProductOptionGroupTableMap;
use Orm\Zed\ProductOption\Persistence\Map\SpyProductOptionGroupTableMap;
use Orm\Zed\ProductOption\Persistence\SpyProductOptionValueQuery;
use Spryker\Zed\Kernel\Persistence\AbstractRepository;

/**
 * @method \Spryker\Zed\ProductOption\Persistence\ProductOptionPersistenceFactory getFactory()
 */
class ProductOptionRepository extends AbstractRepository implements ProductOptionRepositoryInterface
{
    /**
     * @param int[] $productAbstractIds
     *
     * @return \Generated\Shared\Transfer\ProductAbstractOptionGroupStatusTransfer[]
     */
    public function getProductAbstractOptionGroupStatusesByProductAbstractIds(array $productAbstractIds): array
    {
        $productAbstractOptionGroupStatuses = $this->getFactory()
            ->createProductAbstractProductOptionGroupQuery()
            ->filterByFkProductAbstract_In($productAbstractIds)
            ->joinSpyProductOptionGroup()
            ->select([
                ProductAbstractOptionGroupStatusTransfer::ID_PRODUCT_ABSTRACT,
                ProductAbstractOptionGroupStatusTransfer::IS_ACTIVE,
                ProductAbstractOptionGroupStatusTransfer::PRODUCT_OPTION_GROUP_NAME,
            ])
            ->withColumn(SpyProductAbstractProductOptionGroupTableMap::COL_FK_PRODUCT_ABSTRACT, ProductAbstractOptionGroupStatusTransfer::ID_PRODUCT_ABSTRACT)
            ->withColumn(SpyProductOptionGroupTableMap::COL_ACTIVE, ProductAbstractOptionGroupStatusTransfer::IS_ACTIVE)
            ->withColumn(SpyProductOptionGroupTableMap::COL_NAME, ProductAbstractOptionGroupStatusTransfer::PRODUCT_OPTION_GROUP_NAME)
            ->find()
            ->toArray();

        return $this->getFactory()
            ->createProductOptionMapper()
            ->mapProductAbstractOptionGroupStatusesToTransfers($productAbstractOptionGroupStatuses);
    }

    /**
     * @param \Generated\Shared\Transfer\ProductOptionCriteriaTransfer $productOptionCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\ProductOptionValueTransfer[]
     */
    public function get(
        ProductOptionCriteriaTransfer $productOptionCriteriaTransfer
    ): array {
        $productOptionValueQuery = $this->getFactory()
            ->createProductOptionValueQuery()
            ->innerJoinWithSpyProductOptionGroup()
            ->innerJoinWithProductOptionValuePrice();

        $productOptionValueQuery = $this->applyProductOptionCriteriaFilter(
            $productOptionCriteriaTransfer,
            $productOptionValueQuery
        );

        $productOptionValueEntities = $productOptionValueQuery->find();

        return $this->getFactory()
            ->createProductOptionMapper()
            ->mapProductOptionValueEntityCollectionToProductOptionValueTransfers(
                $productOptionValueEntities
            );
    }

    /**
     * @param \Generated\Shared\Transfer\ProductOptionCriteriaTransfer $productOptionCriteriaTransfer
     * @param \Orm\Zed\ProductOption\Persistence\SpyProductOptionValueQuery $productOptionValueQuery
     *
     * @return \Orm\Zed\ProductOption\Persistence\SpyProductOptionValueQuery
     */
    protected function applyProductOptionCriteriaFilter(
        ProductOptionCriteriaTransfer $productOptionCriteriaTransfer,
        SpyProductOptionValueQuery $productOptionValueQuery
    ): SpyProductOptionValueQuery {
        if ($productOptionCriteriaTransfer->getProductOptionGroupIsActive() !== null) {
            $productOptionValueQuery
                ->useSpyProductOptionGroupQuery()
                ->filterByActive($productOptionCriteriaTransfer->getProductOptionGroupIsActive())
                ->endUse();
        }

        if ($productOptionCriteriaTransfer->getProductConcreteSku()) {
            $productOptionValueQuery
                ->useSpyProductOptionGroupQuery()
                ->useSpyProductAbstractProductOptionGroupQuery(null, Criteria::LEFT_JOIN)
                ->useSpyProductAbstractQuery()
                ->useSpyProductQuery()
                ->filterBySku($productOptionCriteriaTransfer->getProductConcreteSku())
                ->endUse()
                ->endUse()
                ->endUse()
                ->endUse();
        }

        if ($productOptionCriteriaTransfer->getProductOptionIds()) {
            $productOptionValueQuery
                ->filterByIdProductOptionValue_In($productOptionCriteriaTransfer->getProductOptionIds());
        }

        if ($productOptionCriteriaTransfer->getProductOptionSkus()) {
            $productOptionValueQuery
                ->filterBySku_In($productOptionCriteriaTransfer->getProductOptionSkus());
        }

        if ($productOptionCriteriaTransfer->getStoreIds()) {
            $productOptionValueQuery
                ->useProductOptionValuePriceQuery()
                ->filterByFkStore_In($productOptionCriteriaTransfer->getStoreIds())
                ->endUse();
        }

        if ($productOptionCriteriaTransfer->getCurrencyIsoCode()) {
            $productOptionValueQuery
                ->useProductOptionValuePriceQuery()
                ->useCurrencyQuery()
                ->filterByCode($productOptionCriteriaTransfer->getCurrencyIsoCode())
                ->endUse()
                ->endUse();
        }

        return $productOptionValueQuery;
    }
}
