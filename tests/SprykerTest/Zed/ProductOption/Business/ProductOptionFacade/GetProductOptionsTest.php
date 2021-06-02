<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductOption\Business\ProductOptionFacade;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\ProductOptionCriteriaTransfer;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductOption
 * @group Business
 * @group ProductOptionFacade
 * @group GetProductOptionsTest
 * Add your own group annotations below this line
 */
class GetProductOptionsTest extends Unit
{
    /**
     * @var \SprykerTest\Zed\ProductOption\ProductOptionBusinessTester
     */
    protected $tester;

    /**
     * @var \Generated\Shared\Transfer\ProductOptionGroupTransfer
     */
    protected $productOptionGroupTransfer;

    /**
     * @var \Generated\Shared\Transfer\ProductOptionGroupTransfer
     */
    protected $anotherProductOptionGroupTransfer;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->productOptionGroupTransfer = $this->tester->haveProductOptionGroupWithValues();
        $this->anotherProductOptionGroupTransfer = $this->tester->haveProductOptionGroupWithValues();
    }

    /**
     * @return void
     */
    public function testGetProductOptionsWillRetrieveExistingOptionBySku(): void
    {
        // Arrange
        $productOptionCriteriaTransfer = (new ProductOptionCriteriaTransfer())
            ->setProductOptionSkus(
                [
                    $this->productOptionGroupTransfer->getProductOptionValues()->offsetGet(0)->getSku(),
                    $this->anotherProductOptionGroupTransfer->getProductOptionValues()->offsetGet(0)->getSku(),
                ]
            );

        // Act
        $productOptionCollectionTransfer = $this->tester
            ->getFacade()
            ->get($productOptionCriteriaTransfer);

        // Assert
        $this->assertNotEmpty($productOptionCollectionTransfer->getProductOptions());
        $this->assertCount(2, $productOptionCollectionTransfer->getProductOptions());
    }

    /**
     * @return void
     */
    public function testGetProductOptionsWillRetrieveExistingOptionById(): void
    {
        // Arrange
        $productOptionCriteriaTransfer = (new ProductOptionCriteriaTransfer())
            ->setProductOptionIds(
                [
                    $this->productOptionGroupTransfer->getProductOptionValues()->offsetGet(0)->getIdProductOptionValue(),
                    $this->anotherProductOptionGroupTransfer->getProductOptionValues()->offsetGet(0)->getIdProductOptionValue(),
                ]
            );

        // Act
        $productOptionCollectionTransfer = $this->tester
            ->getFacade()
            ->get($productOptionCriteriaTransfer);

        // Assert
        $this->assertNotEmpty($productOptionCollectionTransfer->getProductOptions());
        $this->assertCount(2, $productOptionCollectionTransfer->getProductOptions());
    }

    /**
     * @return void
     */
    public function testGetProductOptionsWillRetrieveExistingOptionByIdAndSku(): void
    {
        // Arrange
        $productOptionCriteriaTransfer = (new ProductOptionCriteriaTransfer())
            ->setProductOptionIds(
                [
                    $this->productOptionGroupTransfer->getProductOptionValues()->offsetGet(0)->getIdProductOptionValue(),
                    $this->anotherProductOptionGroupTransfer->getProductOptionValues()->offsetGet(0)->getIdProductOptionValue(),
                ]
            )
            ->setProductOptionSkus(
                [
                    $this->productOptionGroupTransfer->getProductOptionValues()->offsetGet(0)->getSku(),
                    $this->anotherProductOptionGroupTransfer->getProductOptionValues()->offsetGet(0)->getSku(),
                ]
            );

        // Act
        $productOptionCollectionTransfer = $this->tester
            ->getFacade()
            ->get($productOptionCriteriaTransfer);

        // Assert
        $this->assertNotEmpty($productOptionCollectionTransfer->getProductOptions());
        $this->assertCount(2, $productOptionCollectionTransfer->getProductOptions());
    }

    /**
     * @return void
     */
    public function testGetProductOptionsWillNotFindOptionByIdAndSku(): void
    {
        // Arrange
        $productOptionCriteriaTransfer = (new ProductOptionCriteriaTransfer())
            ->setProductOptionIds(
                [
                    99999,
                    $this->productOptionGroupTransfer->getProductOptionValues()->offsetGet(0)->getIdProductOptionValue(),
                ]
            )
            ->setProductOptionSkus(
                [
                    '99999',
                    $this->productOptionGroupTransfer->getProductOptionValues()->offsetGet(0)->getSku(),
                ]
            );

        // Act
        $productOptionCollectionTransfer = $this->tester
            ->getFacade()
            ->get($productOptionCriteriaTransfer);

        // Assert
        $this->assertNotEmpty($productOptionCollectionTransfer->getProductOptions());
        $this->assertCount(1, $productOptionCollectionTransfer->getProductOptions());
    }
}
