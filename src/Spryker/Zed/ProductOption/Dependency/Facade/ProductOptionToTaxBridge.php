<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Dependency\Facade;

class ProductOptionToTaxBridge implements ProductOptionToTaxBridgeInterface
{

    /**
     * @var \Spryker\Zed\Tax\Business\TaxFacadeInterface
     */
    protected $taxFacade;

    /**
     * @param \Spryker\Zed\Tax\Business\TaxFacadeInterface $taxFacade
     */
    public function __construct($taxFacade)
    {
        $this->taxFacade = $taxFacade;
    }

    /**
     * @param int $grossPrice
     * @param float $taxRate
     *
     * @return int
     */
    public function getTaxAmountFromGrossPrice($grossPrice, $taxRate)
    {
        return $this->taxFacade->getTaxAmountFromGrossPrice($grossPrice, $taxRate);
    }

    /**
     * @return string
     */
    public function getDefaultTaxCountry()
    {
        return $this->taxFacade->getDefaultTaxCountry();
    }

    /**
     * @return float
     */
    public function getDefaultTaxRate()
    {
        return $this->taxFacade->getDefaultTaxRate();
    }

}
