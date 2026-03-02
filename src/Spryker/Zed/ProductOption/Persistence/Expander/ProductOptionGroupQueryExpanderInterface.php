<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductOption\Persistence\Expander;

use Propel\Runtime\ActiveQuery\ModelCriteria;

interface ProductOptionGroupQueryExpanderInterface
{
    public function expandQuery(ModelCriteria $query): ModelCriteria;
}
