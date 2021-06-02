<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Business\Reader;

use Generated\Shared\Transfer\ProductOptionCollectionTransfer;
use Generated\Shared\Transfer\ProductOptionCriteriaTransfer;
use Generated\Shared\Transfer\ProductOptionTransfer;
use Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToCurrencyFacadeInterface;
use Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToPriceFacadeInterface;
use Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToStoreFacadeInterface;
use Spryker\Zed\ProductOption\Persistence\ProductOptionRepositoryInterface;

class ProductOptionReader implements ProductOptionReaderInterface
{
    /**
     * @var \Spryker\Zed\ProductOption\Persistence\ProductOptionRepositoryInterface
     */
    protected $productOptionRepository;

    /**
     * @var \Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToCurrencyFacadeInterface
     */
    protected $currencyFacade;

    /**
     * @var \Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToStoreFacadeInterface
     */
    protected $storeFacade;

    /**
     * @var \Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToPriceFacadeInterface
     */
    protected $priceFacade;

    /**
     * @param \Spryker\Zed\ProductOption\Persistence\ProductOptionRepositoryInterface $productOptionRepository
     * @param \Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToCurrencyFacadeInterface $currencyFacade
     * @param \Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToStoreFacadeInterface $storeFacade
     * @param \Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToPriceFacadeInterface $priceFacade
     */
    public function __construct(
        ProductOptionRepositoryInterface $productOptionRepository,
        ProductOptionToCurrencyFacadeInterface $currencyFacade,
        ProductOptionToStoreFacadeInterface $storeFacade,
        ProductOptionToPriceFacadeInterface $priceFacade
    ) {
        $this->productOptionRepository = $productOptionRepository;
        $this->currencyFacade = $currencyFacade;
        $this->storeFacade = $storeFacade;
        $this->priceFacade = $priceFacade;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductOptionCriteriaTransfer $productOptionCriteriaTransfer
     *
     * @return \Generated\Shared\Transfer\ProductOptionCollectionTransfer
     */
    public function get(
        ProductOptionCriteriaTransfer $productOptionCriteriaTransfer
    ): ProductOptionCollectionTransfer {
        $productOptionValueTransfers = $this->productOptionRepository
            ->get($productOptionCriteriaTransfer);

        return $this->createProductOptionCollectionTransfer(
            $productOptionValueTransfers,
            $productOptionCriteriaTransfer->getPriceMode()
        );
    }

    /**
     * @param \Generated\Shared\Transfer\ProductOptionValueTransfer[] $productOptionValueTransfers
     * @param string|null $priceMode
     *
     * @return \Generated\Shared\Transfer\ProductOptionCollectionTransfer
     */
    protected function createProductOptionCollectionTransfer(
        array $productOptionValueTransfers,
        ?string $priceMode = null
    ): ProductOptionCollectionTransfer {
        $productOptionCollectionTransfer = new ProductOptionCollectionTransfer();
        $priceMode = $priceMode ?? $this->priceFacade->getDefaultPriceMode();
        $grossPriceModeIdentifier = $this->priceFacade->getGrossPriceModeIdentifier();

        foreach ($productOptionValueTransfers as $productOptionValueTransfer) {
            $productOptionTransfer = (new ProductOptionTransfer())
                ->fromArray($productOptionValueTransfer->toArray(), true)
                ->setGroupName($productOptionValueTransfer->getProductOptionGroup()->getName());

            if ($productOptionValueTransfer->getPrices()->offsetExists(0)) {
                $moneyValueTransfer = $productOptionValueTransfer->getPrices()->offsetGet(0);
                $productOptionTransfer
                    ->setUnitGrossPrice($moneyValueTransfer->getGrossAmount())
                    ->setUnitNetPrice($moneyValueTransfer->getNetAmount());

                $productOptionTransfer->setUnitPrice(
                    $this->resolveUnitPrice($productOptionTransfer, $priceMode, $grossPriceModeIdentifier)
                );
            }

            $productOptionCollectionTransfer->addProductOption($productOptionTransfer);
        }

        return $productOptionCollectionTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\ProductOptionTransfer $productOptionTransfer
     * @param string $priceMode
     * @param string $grossPriceModeIdentifier
     *
     * @return int|null
     */
    protected function resolveUnitPrice(
        ProductOptionTransfer $productOptionTransfer,
        string $priceMode,
        string $grossPriceModeIdentifier
    ): ?int {
        if ($priceMode === $grossPriceModeIdentifier) {
            return $productOptionTransfer->getUnitGrossPrice();
        }

        return $productOptionTransfer->getUnitNetPrice();
    }
}
