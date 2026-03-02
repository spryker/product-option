<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Dependency\Facade;

use Generated\Shared\Transfer\StoreTransfer;

interface ProductOptionToStoreFacadeInterface
{
    public function getCurrentStore(): StoreTransfer;

    public function getStoreByName(string $storeName): StoreTransfer;
}
