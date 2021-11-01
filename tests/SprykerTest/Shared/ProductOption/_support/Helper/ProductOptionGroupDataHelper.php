<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Shared\ProductOption\Helper;

use ArrayObject;
use Codeception\Module;
use Generated\Shared\DataBuilder\MoneyValueBuilder;
use Generated\Shared\DataBuilder\ProductOptionGroupBuilder;
use Generated\Shared\DataBuilder\ProductOptionTranslationBuilder;
use Generated\Shared\DataBuilder\ProductOptionValueBuilder;
use Generated\Shared\Transfer\ProductOptionGroupTransfer;
use Generated\Shared\Transfer\ProductOptionTranslationTransfer;
use Generated\Shared\Transfer\ProductOptionValueTransfer;
use Orm\Zed\ProductOption\Persistence\SpyProductOptionGroup;
use Spryker\Zed\ProductOption\Business\ProductOptionFacadeInterface;
use SprykerTest\Shared\Testify\Helper\LocatorHelperTrait;

class ProductOptionGroupDataHelper extends Module
{
    use LocatorHelperTrait;

    /**
     * @var string
     */
    public const DEFAULT_STORE = 'DE';

    /**
     * @var string
     */
    public const DEFAULT_CURRENCY = 'EUR';

    /**
     * @var string
     */
    public const CURRENCY_CODE = 'currencyCode';

    /**
     * @var string
     */
    public const STORE_NAME = 'storeName';

    /**
     * @param array $override
     *
     * @return \Generated\Shared\Transfer\ProductOptionGroupTransfer
     */
    public function haveProductOptionGroup(array $override = []): ProductOptionGroupTransfer
    {
        $productOptionGroupTransfer = (new ProductOptionGroupBuilder($override))->build();

        $productOptionGroupTransfer->setIdProductOptionGroup(
            $this->saveGroup($productOptionGroupTransfer),
        );

        return $productOptionGroupTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductOptionGroupTransfer $productOptionGroupTransfer
     *
     * @return int
     */
    protected function saveGroup(ProductOptionGroupTransfer $productOptionGroupTransfer): int
    {
        $entity = new SpyProductOptionGroup();
        $entity->fromArray($productOptionGroupTransfer->toArray());
        $entity->save();

        return $entity->getIdProductOptionGroup();
    }

    /**
     * Example:
     *   This example creates 1 option group with 1 option value and the created option value will contain 1 price:
     *   haveProductOptionGroupWithValues(
     *      ['name' => 'overrideGroupName'],
     *      [
     *          [
     *              ['sku' => 'overrideFirstOptionValueSku'],
     *              [
     *                   ['netAmount' => 1000] // overrides the net amount of the first price of the first option value
     *                   ['storeName' => null, 'netAmount' => 2000, 'currencyCode' => 'USD']
     *                      // defines a default USD price in case USD is not specified directly to a store
     *              ]
     *          ]
     *     ]
     *   )
     *
     * @param array $overrideGroup
     * @param array $overrideValues
     *
     * @return \Generated\Shared\Transfer\ProductOptionGroupTransfer
     */
    public function haveProductOptionGroupWithValues(array $overrideGroup = [], array $overrideValues = [[[], [[]]]]): ProductOptionGroupTransfer
    {
        $productOptionGroupTransfer = (new ProductOptionGroupBuilder($overrideGroup))->build();
        $productOptionGroupTransfer->addGroupNameTranslation(
            $this->createProductOptionTranslationTransfer($productOptionGroupTransfer->getName()),
        );

        $idProductOptionGroup = $this->getProductOptionFacade()->saveProductOptionGroup($productOptionGroupTransfer);
        $productOptionGroupTransfer->setIdProductOptionGroup($idProductOptionGroup);

        foreach ($overrideValues as [$overrideValue, $overridePrices]) {
            $overrideValue = array_merge($overrideValue, [
                ProductOptionValueTransfer::FK_PRODUCT_OPTION_GROUP => $idProductOptionGroup,
            ]);

            $productOptionValueTransfer = $this->createProductOptionValueTransfer($overrideValue, $overridePrices);
            $productOptionGroupTransfer->addProductOptionValue($productOptionValueTransfer);
            $productOptionGroupTransfer->addProductOptionValueTranslation(
                $this->createProductOptionTranslationTransfer($productOptionValueTransfer->getValue()),
            );
        }

        return $productOptionGroupTransfer;
    }

    /**
     * @param array $overrideValue
     * @param array $overridePrices
     *
     * @return \Generated\Shared\Transfer\ProductOptionValueTransfer
     */
    protected function createProductOptionValueTransfer(array $overrideValue = [], array $overridePrices = []): ProductOptionValueTransfer
    {
        $productOptionValueTransfer = (new ProductOptionValueBuilder($overrideValue))
            ->build()
            ->setPrices(new ArrayObject());

        foreach ($overridePrices as $overridePrice) {
            $currencyCode = $overridePrice[static::CURRENCY_CODE] ??
                static::DEFAULT_CURRENCY;
            $storeName = array_key_exists(static::STORE_NAME, $overridePrice) ?
                $overridePrice[static::STORE_NAME] :
                static::DEFAULT_STORE;

            $productOptionValueTransfer->addPrice(
                (new MoneyValueBuilder($overridePrice))
                    ->build()
                    ->setFkCurrency($this->getIdCurrency($currencyCode))
                    ->setFkStore($this->getIdStore($storeName)),
            );
        }

        $idProductOptionValue = $this->getProductOptionFacade()->saveProductOptionValue($productOptionValueTransfer);
        $productOptionValueTransfer->setIdProductOptionValue($idProductOptionValue);

        return $productOptionValueTransfer;
    }

    /**
     * @param string $currencyCode
     *
     * @return int|null
     */
    protected function getIdCurrency(string $currencyCode): ?int
    {
        return $this->getLocator()->currency()->facade()->fromIsoCode($currencyCode)->getIdCurrency();
    }

    /**
     * @param string|null $storeName
     *
     * @return int|null
     */
    protected function getIdStore(?string $storeName): ?int
    {
        if ($storeName === null) {
            return null;
        }

        return $this->getLocator()->store()->facade()->getStoreByName($storeName)->getIdStore();
    }

    /**
     * @param string $key
     *
     * @return \Generated\Shared\Transfer\ProductOptionTranslationTransfer|\Spryker\Shared\Kernel\Transfer\AbstractTransfer
     */
    protected function createProductOptionTranslationTransfer(string $key)
    {
        $override = [
            ProductOptionTranslationTransfer::KEY => $key,
        ];

        return (new ProductOptionTranslationBuilder($override))->build();
    }

    /**
     * @return \Spryker\Zed\ProductOption\Business\ProductOptionFacadeInterface
     */
    public function getProductOptionFacade(): ProductOptionFacadeInterface
    {
        return $this->getLocator()->productOption()->facade();
    }
}
