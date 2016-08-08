<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Communication\Controller;

use Spryker\Zed\Application\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\ProductOption\Communication\ProductOptionCommunicationFactory getFactory()
 * @method \Spryker\Zed\ProductOption\Persistence\ProductOptionQueryContainer getQueryContainer()
 * @method \Spryker\Zed\ProductOption\Business\ProductOptionFacade getFacade()
 */
class IndexController extends AbstractController
{

    const URL_PARAM_ID_PRODUCT_OPTION_GROUP = 'id-product-option-group';
    const URL_PARAM_ACTIVE = 'active';
    const URL_PARAM_REDIRECT_URL = 'redirect-url';
    const URL_PARAM_TABLE_CONTEXT = 'table-context';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function toggleActiveAction(Request $request)
    {
        $idDiscount = $this->castId($request->query->get(self::URL_PARAM_ID_PRODUCT_OPTION_GROUP));
        $isActive = $request->query->get(self::URL_PARAM_ACTIVE);
        $redirectUrl = $request->query->get(self::URL_PARAM_REDIRECT_URL);

        $isChanged = $this->getFacade()->toggleOptionActive($idDiscount, (bool)$isActive);

        if ($isChanged === false) {
            $this->addErrorMessage('Could not activate option.');
        } else {
            $this->addSuccessMessage(sprintf(
                'Option successfully %s.',
                $isActive ? 'activated' : 'deactivated'
            ));
        }

        return new RedirectResponse($redirectUrl);
    }

}
