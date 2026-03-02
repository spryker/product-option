<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Dependency\Facade;

interface ProductOptionToPriceFacadeInterface
{
    public function getGrossPriceModeIdentifier(): string;

    public function getNetPriceModeIdentifier(): string;

    public function getDefaultPriceMode(): string;
}
