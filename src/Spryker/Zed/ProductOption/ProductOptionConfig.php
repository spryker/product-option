<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption;

use Spryker\Zed\Kernel\AbstractBundleConfig;

class ProductOptionConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    public const RESOURCE_TYPE_PRODUCT_OPTION = 'product_option';

    /**
     * @var string
     */
    public const PRODUCT_OPTION_TRANSLATION_PREFIX = 'product.option.';

    /**
     * @var string
     */
    public const PRODUCT_OPTION_GROUP_NAME_TRANSLATION_PREFIX = 'product.option.group.name.';
}
