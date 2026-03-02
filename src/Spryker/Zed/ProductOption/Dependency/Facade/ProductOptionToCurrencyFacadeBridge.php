<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Dependency\Facade;

use Generated\Shared\Transfer\CurrencyTransfer;

class ProductOptionToCurrencyFacadeBridge implements ProductOptionToCurrencyFacadeInterface
{
    /**
     * @var \Spryker\Zed\Currency\Business\CurrencyFacadeInterface
     */
    protected $currencyFacade;

    /**
     * @param \Spryker\Zed\Currency\Business\CurrencyFacadeInterface $currencyFacade
     */
    public function __construct($currencyFacade)
    {
        $this->currencyFacade = $currencyFacade;
    }

    public function getByIdCurrency(int $idCurrency): CurrencyTransfer
    {
        return $this->currencyFacade->getByIdCurrency($idCurrency);
    }

    public function getCurrent(): CurrencyTransfer
    {
        return $this->currencyFacade->getCurrent();
    }

    public function fromIsoCode(string $isoCode): CurrencyTransfer
    {
        return $this->currencyFacade->fromIsoCode($isoCode);
    }
}
