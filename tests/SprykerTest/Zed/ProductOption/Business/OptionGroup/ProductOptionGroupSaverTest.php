<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductOption\Business\OptionGroup;

use ArrayObject;
use Generated\Shared\Transfer\ProductOptionGroupTransfer;
use Generated\Shared\Transfer\ProductOptionTranslationTransfer;
use Generated\Shared\Transfer\ProductOptionValueTransfer;
use Orm\Zed\ProductOption\Persistence\SpyProductOptionGroup;
use Spryker\Zed\ProductOption\Business\Exception\ProductOptionGroupNotFoundException;
use Spryker\Zed\ProductOption\Business\OptionGroup\AbstractProductOptionSaverInterface;
use Spryker\Zed\ProductOption\Business\OptionGroup\ProductOptionGroupSaver;
use Spryker\Zed\ProductOption\Business\OptionGroup\ProductOptionValueSaverInterface;
use Spryker\Zed\ProductOption\Business\OptionGroup\TranslationSaverInterface;
use Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToTouchFacadeInterface;
use Spryker\Zed\ProductOption\Persistence\ProductOptionQueryContainerInterface;
use SprykerTest\Zed\ProductOption\Business\MockProvider;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductOption
 * @group Business
 * @group OptionGroup
 * @group ProductOptionGroupSaverTest
 * Add your own group annotations below this line
 */
class ProductOptionGroupSaverTest extends MockProvider
{
    /**
     * @var int
     */
    protected const VALUE_ID_PRODUCT_OPTION_GROUP = 1;

    /**
     * @var int
     */
    protected const VALUE_IS_ACTIVE = 1;

    /**
     * @return void
     */
    public function testSaveProductOptionGroupShouldSaveGroup(): void
    {
        $translationSaverMock = $this->createTranslationSaverMock();
        $translationSaverMock->expects($this->once())
            ->method('addGroupNameTranslations');

        $translationSaverMock->expects($this->once())
            ->method('addValueTranslations');

        $productOptionGroupSaverMock = $this->createProductOptionGroupSaver(
            null,
            null,
            $translationSaverMock,
        );

        $optionGroupEntityMock = $this->createProductOptionGroupEntityMock();
        $optionGroupEntityMock->method('save')->willReturnCallback(function () use ($optionGroupEntityMock): void {
            $optionGroupEntityMock->setIdProductOptionGroup(1);
        });

        $productOptionGroupSaverMock->expects($this->once())
            ->method('createProductOptionGroupEntity')
            ->willReturn($optionGroupEntityMock);

        $productOptionGroupTransfer = new ProductOptionGroupTransfer();
        $productOptionGroupTransfer->setName('TestGroup');
        $productOptionGroupTransfer->setFkTaxSet(1);

        $productOptionValueTransfer = new ProductOptionValueTransfer();
        $productOptionValueTransfer->setValue('value123');
        $productOptionValueTransfer->setPrices(new ArrayObject());
        $productOptionValueTransfer->setSku('sku123');

        $productOptionGroupTransfer->addProductOptionValue($productOptionValueTransfer);

        $productOptionValueTranslationTransfer = new ProductOptionTranslationTransfer();
        $productOptionValueTranslationTransfer->setName('Name');
        $productOptionValueTranslationTransfer->setKey('Key');
        $productOptionValueTranslationTransfer->setLocaleCode('DE');

        $productOptionGroupTransfer->addProductOptionValueTranslation($productOptionValueTranslationTransfer);

        $productOptionGroupTransfer->setProductsToBeAssigned([1, 2, 3]);

        $idOfPersistedGroup = $productOptionGroupSaverMock->saveProductOptionGroup($productOptionGroupTransfer);

        $this->assertSame($idOfPersistedGroup, 1);
    }

    /**
     * @return void
     */
    public function testToggleActiveShouldPersistCorrectActiveFlag(): void
    {
        $productOptionGroupSaverMock = $this->createProductOptionGroupSaver();

        $productOptionGroupEntityMock = $this->createProductOptionGroupEntityMock();

        $productOptionGroupEntityMock->expects($this->once())
            ->method('save')
            ->willReturn(1);

        $productOptionGroupSaverMock->method('getOptionGroupById')
            ->willReturn($productOptionGroupEntityMock);

        $isActivated = $productOptionGroupSaverMock->toggleOptionActive(static::VALUE_ID_PRODUCT_OPTION_GROUP, static::VALUE_IS_ACTIVE);

        $this->assertTrue($isActivated);
    }

    /**
     * @return void
     */
    public function testToggleActiveShouldThrowExceptionWhenGroupNotFound(): void
    {
        $this->expectException(ProductOptionGroupNotFoundException::class);

        $productOptionGroupSaverMock = $this->createProductOptionGroupSaver();

        $productOptionGroupSaverMock->expects($this->once())
            ->method('getOptionGroupById')
            ->with(static::VALUE_ID_PRODUCT_OPTION_GROUP)
            ->willThrowException(new ProductOptionGroupNotFoundException());

        $productOptionGroupSaverMock->toggleOptionActive(static::VALUE_ID_PRODUCT_OPTION_GROUP, static::VALUE_IS_ACTIVE);
    }

    /**
     * @param \Spryker\Zed\ProductOption\Persistence\ProductOptionQueryContainerInterface|null $productOptionContainerMock
     * @param \Spryker\Zed\ProductOption\Dependency\Facade\ProductOptionToTouchFacadeInterface|null $touchFacadeMock
     * @param \Spryker\Zed\ProductOption\Business\OptionGroup\TranslationSaverInterface|null $translationSaverMock
     * @param \Spryker\Zed\ProductOption\Business\OptionGroup\ProductOptionValueSaverInterface|null $productOptionValueSaverMock
     * @param \Spryker\Zed\ProductOption\Business\OptionGroup\AbstractProductOptionSaverInterface|null $abstractProductOptionSaver
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|\Spryker\Zed\ProductOption\Business\OptionGroup\ProductOptionGroupSaver
     */
    protected function createProductOptionGroupSaver(
        ?ProductOptionQueryContainerInterface $productOptionContainerMock = null,
        ?ProductOptionToTouchFacadeInterface $touchFacadeMock = null,
        ?TranslationSaverInterface $translationSaverMock = null,
        ?ProductOptionValueSaverInterface $productOptionValueSaverMock = null,
        ?AbstractProductOptionSaverInterface $abstractProductOptionSaver = null
    ): ProductOptionGroupSaver {
        if (!$productOptionContainerMock) {
            $productOptionContainerMock = $this->createProductOptionQueryContainerMock();
        }

        if (!$touchFacadeMock) {
            $touchFacadeMock = $this->createTouchFacadeMock();
        }

        if (!$translationSaverMock) {
            $translationSaverMock = $this->createTranslationSaverMock();
        }

        if (!$productOptionValueSaverMock) {
            $productOptionValueSaverMock = $this->createProductOptionValueSaverMock();
        }

        if (!$abstractProductOptionSaver) {
            $abstractProductOptionSaver = $this->createAbstractOptionGroupSaverMock();
        }

        return $this->getMockBuilder(ProductOptionGroupSaver::class)
            ->setConstructorArgs([
                $productOptionContainerMock,
                $touchFacadeMock,
                $translationSaverMock,
                $abstractProductOptionSaver,
                $productOptionValueSaverMock,
                [],
            ])
            ->addMethods([
                'getProductAbstractBySku',
                'getProductOptionValueById',
            ])
            ->onlyMethods([
                'getOptionGroupById',
                'createProductOptionGroupEntity',
            ])
            ->getMock();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Orm\Zed\ProductOption\Persistence\SpyProductOptionGroup
     */
    protected function createProductOptionGroupEntityMock(): SpyProductOptionGroup
    {
        $mockedProductOptionGroup = $this->getMockBuilder(SpyProductOptionGroup::class)
            ->onlyMethods(['save'])
            ->getMock();

        $mockedProductOptionGroup->method('save')
            ->willReturn(1);

        return $mockedProductOptionGroup;
    }
}
