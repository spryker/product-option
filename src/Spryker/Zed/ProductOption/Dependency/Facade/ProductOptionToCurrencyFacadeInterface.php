<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Dependency\Facade;

use Generated\Shared\Transfer\CurrencyTransfer;

interface ProductOptionToCurrencyFacadeInterface
{
    public function getByIdCurrency(int $idCurrency): CurrencyTransfer;

    public function getCurrent(): CurrencyTransfer;

    public function fromIsoCode(string $isoCode): CurrencyTransfer;
}
