<?php
/**
 * Copyright © 2017-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Communication\Plugin\Sales;


use Generated\Shared\Transfer\CheckoutResponseTransfer;
use Generated\Shared\Transfer\OrderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\Sales\Dependency\Plugin\HydrateOrderPluginInterface;

/**
 * @method \Spryker\Zed\ProductOption\Business\ProductOptionFacade getFacade()
 * @method \Spryker\Zed\ProductOption\Communication\ProductOptionCommunicationFactory getFactory()
 */
class ProductOptionOrderHydrate extends AbstractPlugin implements HydrateOrderPluginInterface
{

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\OrderTransfer
     */
    public function hydrate(OrderTransfer $orderTransfer)
    {
        return $this->getFacade()
            ->hydrateSalesOrderProductOptions($orderTransfer);
    }
}
