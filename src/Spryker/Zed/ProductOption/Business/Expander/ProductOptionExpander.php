<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Business\Expander;

use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\ProductOptionTransfer;
use Spryker\Zed\ProductOption\Persistence\ProductOptionRepositoryInterface;

class ProductOptionExpander implements ProductOptionExpanderInterface
{
    /**
     * @var \Spryker\Zed\ProductOption\Persistence\ProductOptionRepositoryInterface
     */
    protected $productOptionRepository;

    /**
     * @param \Spryker\Zed\ProductOption\Persistence\ProductOptionRepositoryInterface $productOptionRepository
     */
    public function __construct(ProductOptionRepositoryInterface $productOptionRepository)
    {
        $this->productOptionRepository = $productOptionRepository;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     *
     * @return \Generated\Shared\Transfer\ItemTransfer[]
     */
    public function expandOrderItemsWithProductOptions(array $itemTransfers): array
    {
        $mappedOrderItemsWithProductOptions = $this->getMappedOrderItemsWithProductOptions($itemTransfers);
        $mappedProductOptionValueTransfers = $this->getMappedProductOptionValues($mappedOrderItemsWithProductOptions);

        foreach ($itemTransfers as $itemTransfer) {
            $orderItemWithProductOptions = $mappedOrderItemsWithProductOptions[$itemTransfer->getIdSalesOrderItem()] ?? null;

            if ($orderItemWithProductOptions) {
                $this->expandOrderItemWithProductOptions($itemTransfer, $orderItemWithProductOptions, $mappedProductOptionValueTransfers);
            }
        }

        return $itemTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     * @param \Generated\Shared\Transfer\ItemTransfer $orderItemWithProductOptions
     * @param \Generated\Shared\Transfer\ProductOptionValueTransfer[] $productOptionValueTransfers
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function expandOrderItemWithProductOptions(
        ItemTransfer $itemTransfer,
        ItemTransfer $orderItemWithProductOptions,
        array $productOptionValueTransfers
    ): ItemTransfer {
        $itemTransfer
            ->setProductOptions($orderItemWithProductOptions->getProductOptions())
            ->setSumProductOptionPriceAggregation($orderItemWithProductOptions->getSumProductOptionPriceAggregation())
            ->setUnitProductOptionPriceAggregation(
                (int)round($orderItemWithProductOptions->getSumProductOptionPriceAggregation() / $itemTransfer->getQuantity())
            );

        foreach ($itemTransfer->getProductOptions() as $productOptionTransfer) {
            $productOptionValueTransfer = $productOptionValueTransfers[$productOptionTransfer->getSku()] ?? null;

            if ($productOptionValueTransfer) {
                $productOptionTransfer->setIdProductOptionValue($productOptionValueTransfer->getIdProductOptionValue());
            }

            $this->deriveProductOptionUnitPrices($productOptionTransfer);
        }

        return $itemTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     *
     * @return \Generated\Shared\Transfer\ItemTransfer[]
     */
    protected function getMappedOrderItemsWithProductOptions(array $itemTransfers): array
    {
        $salesOrderItemIds = $this->extractSalesOrderItemIds($itemTransfers);
        $orderItemsWithProductOptions = $this->productOptionRepository->getOrderItemsWithProductOptions($salesOrderItemIds);

        $mappedItemTransfers = [];

        foreach ($orderItemsWithProductOptions as $orderItemWithProductOptions) {
            $mappedItemTransfers[$orderItemWithProductOptions->getIdSalesOrderItem()] = $orderItemWithProductOptions;
        }

        return $mappedItemTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     *
     * @return \Generated\Shared\Transfer\ProductOptionValueTransfer[]
     */
    protected function getMappedProductOptionValues(array $itemTransfers): array
    {
        $productOptionSkus = $this->extractProductOptionSkus($itemTransfers);
        $productOptionValueTransfers = $this->productOptionRepository->getProductOptionValuesBySkus($productOptionSkus);

        $mappedProductOptionValueTransfers = [];

        foreach ($productOptionValueTransfers as $productOptionValueTransfer) {
            $mappedProductOptionValueTransfers[$productOptionValueTransfer->getSku()] = $productOptionValueTransfer;
        }

        return $mappedProductOptionValueTransfers;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     *
     * @return int[]
     */
    protected function extractSalesOrderItemIds(array $itemTransfers): array
    {
        $salesOrderItemIds = [];

        foreach ($itemTransfers as $itemTransfer) {
            $salesOrderItemIds[] = $itemTransfer->getIdSalesOrderItem();
        }

        return array_unique($salesOrderItemIds);
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer[] $itemTransfers
     *
     * @return string[]
     */
    protected function extractProductOptionSkus(array $itemTransfers): array
    {
        $productOptionSkus = [];

        foreach ($itemTransfers as $itemTransfer) {
            foreach ($itemTransfer->getProductOptions() as $productOptionTransfer) {
                $productOptionSkus[] = $productOptionTransfer->getSku();
            }
        }

        return array_unique($productOptionSkus);
    }

    /**
     * Unit prices are populated for presentation purposes only. For further calculations use sum prices or properly populated unit prices.
     *
     * @param \Generated\Shared\Transfer\ProductOptionTransfer $productOptionTransfer
     *
     * @return \Generated\Shared\Transfer\ProductOptionTransfer
     */
    protected function deriveProductOptionUnitPrices(ProductOptionTransfer $productOptionTransfer): ProductOptionTransfer
    {
        $productOptionTransfer
            ->setUnitPrice((int)round($productOptionTransfer->getSumPrice() / $productOptionTransfer->getQuantity()))
            ->setUnitGrossPrice((int)round($productOptionTransfer->getSumGrossPrice() / $productOptionTransfer->getQuantity()))
            ->setUnitNetPrice((int)round($productOptionTransfer->getSumNetPrice() / $productOptionTransfer->getQuantity()))
            ->setUnitDiscountAmountAggregation((int)round($productOptionTransfer->getSumDiscountAmountAggregation() / $productOptionTransfer->getQuantity()))
            ->setUnitTaxAmount((int)round($productOptionTransfer->getSumTaxAmount() / $productOptionTransfer->getQuantity()));

        return $productOptionTransfer;
    }
}
