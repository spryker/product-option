<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Business\OptionGroup;

use Generated\Shared\Transfer\ProductOptionCollectionTransfer;
use Generated\Shared\Transfer\ProductOptionCriteriaTransfer;
use Generated\Shared\Transfer\ProductOptionTransfer;

interface ProductOptionValueReaderInterface
{
    public function findProductOptionByIdProductOptionValue(int $idProductOptionValue): ?ProductOptionTransfer;

    public function checkProductOptionValueExistence(int $idProductOptionValue): bool;

    /**
     * @param int $idProductOptionValue
     * @param string|null $currencyCode
     *
     * @throws \Spryker\Zed\ProductOption\Business\Exception\ProductOptionNotFoundException
     *
     * @return \Generated\Shared\Transfer\ProductOptionTransfer
     */
    public function getProductOption($idProductOptionValue, ?string $currencyCode = null);

    public function getProductOptionCollectionByProductOptionCriteria(
        ProductOptionCriteriaTransfer $productOptionCriteriaTransfer
    ): ProductOptionCollectionTransfer;
}
