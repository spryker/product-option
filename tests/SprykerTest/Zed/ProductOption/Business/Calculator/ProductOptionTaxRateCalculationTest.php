<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductOption\Business\Calculator;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\AddressTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\ProductOptionTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentTransfer;
use Spryker\Zed\ProductOption\Business\Calculator\ProductOptionTaxRateCalculator;
use Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToStoreFacadeBridge;
use Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToTaxFacadeBridge;
use Spryker\Zed\ProductOption\Persistence\ProductOptionQueryContainer;
use SprykerTest\Zed\ProductOption\ProductOptionBusinessTester;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductOption
 * @group Business
 * @group Calculator
 * @group ProductOptionTaxRateCalculationTest
 * Add your own group annotations below this line
 */
class ProductOptionTaxRateCalculationTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\ProductOption\ProductOptionBusinessTester
     */
    protected ProductOptionBusinessTester $tester;

    /**
     * @return void
     */
    public function testCalculateTaxRateForDefaultCountry(): void
    {
        $quoteTransfer = $this->createQuoteTransferWithoutShippingAddress();

        $taxAverage = $this->getEffectiveTaxRateByQuoteTransfer($quoteTransfer, $this->getMockDefaultTaxRates());
        $this->assertSame(15.0, $taxAverage);
    }

    /**
     * @return void
     */
    public function testCalculateTaxRateForDifferentCountry(): void
    {
        $quoteTransfer = $this->createQuoteTransferWithShippingAddress();

        $taxAverage = $this->getEffectiveTaxRateByQuoteTransfer($quoteTransfer, $this->getMockCountryBasedTaxRates());
        $this->assertSame(17.0, $taxAverage);
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param array $mockData
     *
     * @return float
     */
    protected function getEffectiveTaxRateByQuoteTransfer(QuoteTransfer $quoteTransfer, array $mockData): float
    {
        $productItemTaxRateCalculatorMock = $this->createProductItemTaxRateCalculator();
        $productItemTaxRateCalculatorMock->method('findTaxRatesByIdOptionValueAndCountryIso2Code')->willReturn($mockData);

        $productItemTaxRateCalculatorMock->recalculate($quoteTransfer);
        $taxAverage = $this->getProductItemsTaxRateAverage($quoteTransfer);

        return $taxAverage;
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\PHPUnit\Framework\MockObject\InvocationMocker|\Spryker\Zed\ProductOption\Business\Calculator\ProductOptionTaxRateCalculator
     */
    protected function createProductItemTaxRateCalculator()
    {
        return $this->getMockBuilder(ProductOptionTaxRateCalculator::class)
            ->onlyMethods(['findTaxRatesByIdOptionValueAndCountryIso2Code'])
            ->setConstructorArgs([
                $this->createQueryContainerMock(),
                $this->createProductOptionToTaxBridgeMock(),
                new ProductOptionToStoreFacadeBridge($this->tester->getLocator()->store()->facade()),
            ])
            ->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\PHPUnit\Framework\MockObject\InvocationMocker|\Spryker\Zed\ProductOption\Persistence\ProductOptionQueryContainerInterface
     */
    protected function createQueryContainerMock()
    {
        return $this->getMockBuilder(ProductOptionQueryContainer::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\PHPUnit\Framework\MockObject\InvocationMocker|\Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToTaxFacadeBridge
     */
    protected function createProductOptionToTaxBridgeMock()
    {
        $bridgeMock = $this->getMockBuilder(ProductOptionToTaxFacadeBridge::class)
            ->disableOriginalConstructor()
            ->getMock();

        $bridgeMock
            ->expects($this->any())
            ->method('getDefaultTaxCountryIso2Code')
            ->willReturn('DE');

        $bridgeMock
            ->expects($this->any())
            ->method('getDefaultTaxRate')
            ->willReturn(19.0);

        return $bridgeMock;
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransferWithoutShippingAddress(): QuoteTransfer
    {
        $quoteTransfer = $this->createQuoteTransfer();

        $this->addItemTransfers($quoteTransfer);

        return $quoteTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransferWithShippingAddress(): QuoteTransfer
    {
        $quoteTransfer = $this->createQuoteTransfer();

        $addressTransfer = new AddressTransfer();
        $addressTransfer->setIso2Code('AT');

        $this->addItemTransfers($quoteTransfer, $addressTransfer);

        return $quoteTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     * @param \Generated\Shared\Transfer\AddressTransfer|null $addressTransfer
     *
     * @return void
     */
    protected function addItemTransfers(QuoteTransfer $quoteTransfer, ?AddressTransfer $addressTransfer = null): void
    {
        $itemTransfer1 = $this->createProductItemTransfer(1, $addressTransfer);
        $itemTransfer1->addProductOption($this->createProductOption(1));
        $quoteTransfer->addItem($itemTransfer1);

        $itemTransfer2 = $this->createProductItemTransfer(2, $addressTransfer);
        $itemTransfer2->addProductOption($this->createProductOption(2));
        $quoteTransfer->addItem($itemTransfer2);
    }

    /**
     * @param int $id
     * @param \Generated\Shared\Transfer\AddressTransfer|null $addressTransfer
     *
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function createProductItemTransfer(int $id, ?AddressTransfer $addressTransfer = null): ItemTransfer
    {
        $itemTransfer = $this->createItemTransfer();
        $itemTransfer->setIdProductAbstract($id);
        $shipmentTransfer = $this->createShipment($addressTransfer);
        $itemTransfer->setShipment($shipmentTransfer);

        return $itemTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AddressTransfer|null $addressTransfer
     *
     * @return \Generated\Shared\Transfer\ShipmentTransfer
     */
    protected function createShipment(?AddressTransfer $addressTransfer = null): ShipmentTransfer
    {
        $shipmentTransfer = $this->createShipmentTransfer();
        if ($addressTransfer !== null) {
            $shipmentTransfer->setShippingAddress($addressTransfer);
        }

        return $shipmentTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransfer(): QuoteTransfer
    {
        return new QuoteTransfer();
    }

    /**
     * @return \Generated\Shared\Transfer\ShipmentTransfer
     */
    protected function createShipmentTransfer(): ShipmentTransfer
    {
        return new ShipmentTransfer();
    }

    /**
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function createItemTransfer(): ItemTransfer
    {
        return new ItemTransfer();
    }

    /**
     * @return array
     */
    protected function getMockDefaultTaxRates(): array
    {
        return [
            [
                ProductOptionQueryContainer::COL_ID_PRODUCT_OPTION_VALUE => 1,
                ProductOptionQueryContainer::COL_MAX_TAX_RATE => 11,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getMockCountryBasedTaxRates(): array
    {
        return [
            [
                ProductOptionQueryContainer::COL_ID_PRODUCT_OPTION_VALUE => 1,
                ProductOptionQueryContainer::COL_MAX_TAX_RATE => 20,
            ],
            [
                ProductOptionQueryContainer::COL_ID_PRODUCT_OPTION_VALUE => 2,
                ProductOptionQueryContainer::COL_MAX_TAX_RATE => 14,
            ],
        ];
    }

    /**
     * @param int $idOptionValueUsage
     *
     * @return \Generated\Shared\Transfer\ProductOptionTransfer
     */
    protected function createProductOption(int $idOptionValueUsage): ProductOptionTransfer
    {
        $productOption1 = new ProductOptionTransfer();
        $productOption1->setIdProductOptionValue($idOptionValueUsage);

        return $productOption1;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return float
     */
    protected function getProductItemsTaxRateAverage(QuoteTransfer $quoteTransfer): float
    {
        $taxSum = 0;
        $productOptionCount = 0;
        foreach ($quoteTransfer->getItems() as $item) {
            $taxSum += $this->getEffectiveProductOptionTaxRate($item);
            $productOptionCount += count($item->getProductOptions());
        }

        $taxAverage = $taxSum / $productOptionCount;

        return $taxAverage;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $item
     *
     * @return float
     */
    protected function getEffectiveProductOptionTaxRate(ItemTransfer $item): float
    {
        $taxSum = 0;
        foreach ($item->getProductOptions() as $productOption) {
            $taxSum += $productOption->getTaxRate();
        }

        return $taxSum;
    }
}
