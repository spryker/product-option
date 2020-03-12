<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Business;

use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\ProductOptionCollectionTransfer;
use Generated\Shared\Transfer\ProductOptionCriteriaTransfer;
use Generated\Shared\Transfer\ProductOptionGroupTransfer;
use Generated\Shared\Transfer\ProductOptionTransfer;
use Generated\Shared\Transfer\ProductOptionValueStorePricesRequestTransfer;
use Generated\Shared\Transfer\ProductOptionValueTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\SaveOrderTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Spryker\Zed\ProductOption\Business\ProductOptionBusinessFactory getFactory()
 * @method \Spryker\Zed\ProductOption\Persistence\ProductOptionRepositoryInterface getRepository()
 */
class ProductOptionFacade extends AbstractFacade implements ProductOptionFacadeInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductOptionGroupTransfer $productOptionGroupTransfer
     *
     * @return int
     */
    public function saveProductOptionGroup(ProductOptionGroupTransfer $productOptionGroupTransfer)
    {
        return $this->getFactory()
           ->createProductOptionGroupSaver()
           ->saveProductOptionGroup($productOptionGroupTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductOptionValueTransfer $productOptionValueTransfer
     *
     * @return int
     */
    public function saveProductOptionValue(ProductOptionValueTransfer $productOptionValueTransfer)
    {
        return $this->getFactory()
            ->createProductOptionValueSaver()
            ->saveProductOptionValue($productOptionValueTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param string $abstractSku
     * @param int $idProductOptionGroup
     *
     * @return bool
     */
    public function addProductAbstractToProductOptionGroup($abstractSku, $idProductOptionGroup)
    {
        return $this->getFactory()
            ->createAbstractProductOptionSaver()
            ->addProductAbstractToProductOptionGroup($abstractSku, $idProductOptionGroup);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idProductOptionValue
     * @param string|null $currencyCode
     *
     * @return \Generated\Shared\Transfer\ProductOptionTransfer
     */
    public function getProductOptionValueById($idProductOptionValue, ?string $currencyCode = null)
    {
        return $this->getFactory()
            ->createProductOptionValueReader()
            ->getProductOption($idProductOptionValue, $currencyCode);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idProductOptionGroup
     *
     * @return \Generated\Shared\Transfer\ProductOptionGroupTransfer
     */
    public function getProductOptionGroupById($idProductOptionGroup)
    {
        return $this->getFactory()
            ->createProductOptionGroupReader()
            ->getProductOptionGroupById($idProductOptionGroup);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @deprecated Use saveOrderProductOptions() instead
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\CheckoutResponseTransfer $checkoutResponse
     *
     * @return void
     */
    public function saveSaleOrderProductOptions(QuoteTransfer $quoteTransfer, CheckoutResponseTransfer $checkoutResponse)
    {
        $this->getFactory()
            ->createProductOptionOrderSaver()
            ->save($quoteTransfer, $checkoutResponse);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\SaveOrderTransfer $saveOrderTransfer
     *
     * @return void
     */
    public function saveOrderProductOptions(QuoteTransfer $quoteTransfer, SaveOrderTransfer $saveOrderTransfer)
    {
        $this->getFactory()
            ->createPlaceOrderProductOptionOrderSaver()
            ->saveOrderProductOptions($quoteTransfer, $saveOrderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    public function calculateProductOptionTaxRate(QuoteTransfer $quoteTransfer)
    {
        $this->getFactory()
            ->createProductItemTaxRateCalculatorStrategyResolver()
            ->resolve($quoteTransfer->getItems())
            ->recalculate($quoteTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idProductOptionGroup
     * @param bool $isActive
     *
     * @return bool
     */
    public function toggleOptionActive($idProductOptionGroup, $isActive)
    {
        return $this->getFactory()
            ->createProductOptionGroupSaver()
            ->toggleOptionActive($idProductOptionGroup, $isActive);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function hydrateSalesOrderProductOptions(OrderTransfer $orderTransfer)
    {
        return $this->getFactory()
            ->createProductOptionOrderHydrate()
            ->hydrate($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @deprecated Not used anymore.
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function sortSalesOrderItemsByOptions(OrderTransfer $orderTransfer)
    {
        return $this->getFactory()
            ->createProductOptionItemSorter()
            ->sortItemsBySkuAndOptions($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function hydrateProductOptionGroupIds(OrderTransfer $orderTransfer)
    {
        return $this->getFactory()
            ->createProductOptionGroupIdHydrator()
            ->hydrateProductOptionGroupIds($orderTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductOptionValueStorePricesRequestTransfer $storePricesRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ProductOptionValueStorePricesResponseTransfer
     */
    public function getProductOptionValueStorePrices(ProductOptionValueStorePricesRequestTransfer $storePricesRequestTransfer)
    {
        return $this->getFactory()
            ->createProductOptionValuePriceReader()
            ->getStorePrices($storePricesRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductOptionValueStorePricesRequestTransfer $storePricesRequestTransfer
     *
     * @return \Generated\Shared\Transfer\ProductOptionValueStorePricesResponseTransfer
     */
    public function getAllProductOptionValuePrices(ProductOptionValueStorePricesRequestTransfer $storePricesRequestTransfer)
    {
        return $this->getFactory()
            ->createProductOptionValuePriceReader()
            ->getAllPrices($storePricesRequestTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\ProductOptionCriteriaTransfer $productOptionCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\ProductOptionCollectionTransfer
     */
    public function getProductOptionCollectionByProductOptionCriteria(
        ProductOptionCriteriaTransfer $productOptionCriteriaTransfer
    ): ProductOptionCollectionTransfer {
        return $this->getFactory()
            ->createProductOptionValueReader()
            ->getProductOptionCollectionByProductOptionCriteria($productOptionCriteriaTransfer);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idProductOptionValue
     *
     * @return \Generated\Shared\Transfer\ProductOptionTransfer|null
     */
    public function findProductOptionByIdProductOptionValue(int $idProductOptionValue): ?ProductOptionTransfer
    {
        return $this->getFactory()
            ->createProductOptionValueReader()
            ->findProductOptionByIdProductOptionValue($idProductOptionValue);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @deprecated Use checkProductOptionGroupExistenceByProductOptionValueId() instead
     *
     * @param int $idProductOptionValue
     *
     * @return bool
     */
    public function checkProductOptionValueExistence(int $idProductOptionValue): bool
    {
        return $this->getFactory()
            ->createProductOptionValueReader()
            ->checkProductOptionValueExistence($idProductOptionValue);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int $idProductOptionValue
     *
     * @return bool
     */
    public function checkProductOptionGroupExistenceByProductOptionValueId(int $idProductOptionValue): bool
    {
        return $this->getFactory()
            ->createProductOptionGroupReader()
            ->checkProductOptionGroupExistenceByProductOptionValueId($idProductOptionValue);
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @param int[] $productAbstractIds
     *
     * @return \Generated\Shared\Transfer\ProductAbstractOptionGroupStatusTransfer[]
     */
    public function getProductAbstractOptionGroupStatusesByProductAbstractIds(array $productAbstractIds): array
    {
        return $this->getRepository()
            ->getProductAbstractOptionGroupStatusesByProductAbstractIds($productAbstractIds);
    }
}
