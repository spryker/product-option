<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Shared\ProductOption\Helper;

use ArrayObject;
use Codeception\Module;
use Generated\Shared\DataBuilder\ProductOptionGroupBuilder;
use Generated\Shared\Transfer\CurrencyTransfer;
use Generated\Shared\Transfer\MoneyValueTransfer;
use Generated\Shared\Transfer\ProductOptionGroupTransfer;
use Generated\Shared\Transfer\ProductOptionValueTransfer;
use Generated\Shared\Transfer\StoreTransfer;
use Spryker\Zed\ProductOption\Business\ProductOptionFacadeInterface;
use SprykerTest\Shared\Testify\Helper\DataCleanupHelperTrait;
use SprykerTest\Shared\Testify\Helper\LocatorHelperTrait;

class ProductOptionDataHelper extends Module
{
    use DataCleanupHelperTrait;
    use LocatorHelperTrait;

    public function haveProductOptionGroup(array $productOptionGroupOverride = []): ProductOptionGroupTransfer
    {
        $productOptionGroupTransfer = (new ProductOptionGroupBuilder($productOptionGroupOverride))
            ->withProductOptionValue()
            ->withAnotherProductOptionValue()
            ->withGroupNameTranslation()
            ->withProductOptionValueTranslation()
            ->build();

        $idProductOptionGroup = $this->getProductOptionFacade()->saveProductOptionGroup($productOptionGroupTransfer);

        $productOptionGroupTransfer->setIdProductOptionGroup($idProductOptionGroup);

        return $productOptionGroupTransfer;
    }

    public function haveProductOptionValueForAbstractProduct(
        string $productAbstractSku,
        StoreTransfer $storeTransfer
    ): ProductOptionValueTransfer {
        $currencyTransfer = $this->getDefaultStoreCurrency($storeTransfer);

        $productOptionGroupTransfer = (new ProductOptionGroupBuilder())
            ->withProductOptionValue([
                ProductOptionValueTransfer::PRICES => new ArrayObject([
                    [
                        MoneyValueTransfer::NET_AMOUNT => 100,
                        MoneyValueTransfer::GROSS_AMOUNT => 100,
                        MoneyValueTransfer::FK_STORE => $storeTransfer->getIdStoreOrFail(),
                        MoneyValueTransfer::FK_CURRENCY => $currencyTransfer->getIdCurrencyOrFail(),
                    ],
                ]),
            ])
            ->withGroupNameTranslation()
            ->withProductOptionValueTranslation()
            ->build();

        $idProductOptionGroup = $this->getProductOptionFacade()->saveProductOptionGroup($productOptionGroupTransfer);
        $productOptionGroupTransfer->setIdProductOptionGroup($idProductOptionGroup);

        $this->getProductOptionFacade()->addProductAbstractToProductOptionGroup(
            $productAbstractSku,
            $idProductOptionGroup,
        );

        return $productOptionGroupTransfer->getProductOptionValues()->getIterator()->current();
    }

    protected function getDefaultStoreCurrency(StoreTransfer $storeTransfer): CurrencyTransfer
    {
        $currencyTransfers = $this->getLocator()->currency()->facade()->getCurrencyTransfersByIsoCodes([
            $storeTransfer->getDefaultCurrencyIsoCodeOrFail(),
        ]);

        return array_shift($currencyTransfers);
    }

    protected function getProductOptionFacade(): ProductOptionFacadeInterface
    {
        return $this->getLocator()->productOption()->facade();
    }
}
