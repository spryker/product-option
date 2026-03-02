<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Business\Replacer;

use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Kernel\Persistence\EntityManager\TransactionTrait;
use Spryker\Zed\ProductOption\Business\PlaceOrder\ProductOptionOrderSaverInterface;
use Spryker\Zed\ProductOption\Persistence\ProductOptionEntityManagerInterface;

class SalesOrderItemOptionReplacer implements SalesOrderItemOptionReplacerInterface
{
    use TransactionTrait;

    public function __construct(
        protected ProductOptionEntityManagerInterface $productOptionEntityManager,
        protected ProductOptionOrderSaverInterface $productOptionOrderSaver
    ) {
    }

    public function replaceSalesOrderItemOptions(QuoteTransfer $quoteTransfer): void
    {
        $salesOrderItemIds = $this->extractSalesOrderItemIds($quoteTransfer);
        if (!$salesOrderItemIds) {
            return;
        }

        $quoteTransfer = $this->unsetSalesOrderItemOptionIds($quoteTransfer);

        $this->getTransactionHandler()->handleTransaction(function () use ($salesOrderItemIds, $quoteTransfer): void {
            $this->executeReplaceSalesOrderItemOptionsTransaction($salesOrderItemIds, $quoteTransfer);
        });
    }

    /**
     * @param array<int> $salesOrderItemIds
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return void
     */
    protected function executeReplaceSalesOrderItemOptionsTransaction(
        array $salesOrderItemIds,
        QuoteTransfer $quoteTransfer
    ): void {
        $this->productOptionEntityManager->deleteSalesOrderItemProductOptionsBySalesOrderItemIds($salesOrderItemIds);
        $this->productOptionOrderSaver->createSalesOrderItemOptions($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return array<int>
     */
    protected function extractSalesOrderItemIds(QuoteTransfer $quoteTransfer): array
    {
        $salesOrderItemIds = [];
        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            $salesOrderItemIds[] = $itemTransfer->getIdSalesOrderItemOrFail();
        }

        return array_unique($salesOrderItemIds);
    }

    protected function unsetSalesOrderItemOptionIds(QuoteTransfer $quoteTransfer): QuoteTransfer
    {
        foreach ($quoteTransfer->getItems() as $itemTransfer) {
            foreach ($itemTransfer->getProductOptions() as $productOptionTransfer) {
                $productOptionTransfer->setIdSalesOrderItemOption(null);
            }
        }

        return $quoteTransfer;
    }
}
