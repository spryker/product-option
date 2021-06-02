<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Persistence\Propel\Mapper;

use ArrayObject;
use Generated\Shared\Transfer\MoneyValueTransfer;
use Generated\Shared\Transfer\ProductAbstractOptionGroupStatusTransfer;
use Generated\Shared\Transfer\ProductOptionGroupTransfer;
use Generated\Shared\Transfer\ProductOptionValueTransfer;
use Propel\Runtime\Collection\ObjectCollection;

class ProductOptionMapper
{
    /**
     * @param array $productAbstractOptionGroupStatuses
     *
     * @return array
     */
    public function mapProductAbstractOptionGroupStatusesToTransfers(
        array $productAbstractOptionGroupStatuses
    ): array {
        $productAbstractOptionGroupStatusTransfers = [];
        foreach ($productAbstractOptionGroupStatuses as $productAbstractOptionGroupStatus) {
            $productAbstractOptionGroupStatusTransfers[] = $this->mapProductAbstractOptionGroupStatusToTransfer(
                $productAbstractOptionGroupStatus
            );
        }

        return $productAbstractOptionGroupStatusTransfers;
    }

    /**
     * @param array $productAbstractOptionGroupStatus
     *
     * @return \Generated\Shared\Transfer\ProductAbstractOptionGroupStatusTransfer
     */
    protected function mapProductAbstractOptionGroupStatusToTransfer(
        array $productAbstractOptionGroupStatus
    ): ProductAbstractOptionGroupStatusTransfer {
        return (new ProductAbstractOptionGroupStatusTransfer())
            ->fromArray($productAbstractOptionGroupStatus);
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection|\Orm\Zed\ProductOption\Persistence\SpyProductOptionValue[] $productOptionValueEntities
     *
     * @return \Generated\Shared\Transfer\ProductOptionValueTransfer[]
     */
    public function mapProductOptionValueEntityCollectionToProductOptionValueTransfers(
        ObjectCollection $productOptionValueEntities
    ): array {
        $productOptionValueTransfers = [];

        foreach ($productOptionValueEntities as $productOptionValueEntity) {
            $productOptionValueTransfers[] = (new ProductOptionValueTransfer())
                ->fromArray($productOptionValueEntity->toArray(), true)
                ->setProductOptionGroup(
                    (new ProductOptionGroupTransfer())
                        ->fromArray($productOptionValueEntity->getSpyProductOptionGroup()->toArray(), true)
                )
                ->setPrices(
                    $this->mapProductOptionValuePriceEntitiesToMoneyValueTransfers(
                        $productOptionValueEntity->getProductOptionValuePrices()
                    )
                );
        }

        return $productOptionValueTransfers;
    }

    /**
     * @param \Propel\Runtime\Collection\ObjectCollection|\Orm\Zed\ProductOption\Persistence\SpyProductOptionValuePrice[] $productOptionValuePriceEntities
     *
     * @return \ArrayObject|\Generated\Shared\Transfer\MoneyValueTransfer[]
     */
    protected function mapProductOptionValuePriceEntitiesToMoneyValueTransfers(
        ObjectCollection $productOptionValuePriceEntities
    ): ArrayObject {
        $moneyValueTransfers = new ArrayObject();

        foreach ($productOptionValuePriceEntities as $productOptionValuePriceEntity) {
            $moneyValueTransfers->append(
                (new MoneyValueTransfer())
                    ->fromArray($productOptionValuePriceEntity->toArray(), true)
                    ->setGrossAmount($productOptionValuePriceEntity->getGrossPrice())
                    ->setNetAmount($productOptionValuePriceEntity->getNetPrice())
            );
        }

        return $moneyValueTransfers;
    }
}
