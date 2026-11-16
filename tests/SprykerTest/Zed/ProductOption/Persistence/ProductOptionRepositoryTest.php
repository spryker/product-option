<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerTest\Zed\ProductOption\Persistence;

use Codeception\Test\Unit;
use Generated\Shared\Transfer\QueryCriteriaTransfer;
use Generated\Shared\Transfer\QueryJoinTransfer;
use Generated\Shared\Transfer\QueryWhereConditionTransfer;
use Orm\Zed\ProductOption\Persistence\Map\SpyProductOptionGroupTableMap;
use Orm\Zed\ProductOption\Persistence\Map\SpyProductOptionValueTableMap;
use Orm\Zed\ProductOption\Persistence\SpyProductOptionGroupQuery;
use Spryker\Zed\ProductOption\Persistence\ProductOptionRepository;
use Spryker\Zed\ProductOption\ProductOptionDependencyProvider;
use Spryker\Zed\ProductOptionGuiExtension\Dependency\Plugin\ProductOptionListTableQueryCriteriaExpanderPluginInterface;
use Spryker\Zed\PropelOrm\Business\Runtime\ActiveQuery\Criteria;

/**
 * Auto-generated group annotations
 *
 * @group SprykerTest
 * @group Zed
 * @group ProductOption
 * @group Persistence
 * @group ProductOptionRepositoryTest
 * Add your own group annotations below this line
 */
class ProductOptionRepositoryTest extends Unit
{
    protected const string WHERE_CONDITION_VALUE_TEMPLATE_WITH_SQL_TAIL = '%d OR 1=1';

    /**
     * @var \SprykerTest\Zed\ProductOption\ProductOptionPersistenceTester
     */
    protected $tester;

    public function testExpandProductOptionGroupQueryFiltersByJoinWhereConditionValue(): void
    {
        // Arrange
        $idProductOptionGroup = $this->tester->haveProductOptionGroup()->getIdProductOptionGroupOrFail();
        $this->tester->haveProductOptionGroup();
        $this->setJoinWhereConditionValue((string)$idProductOptionGroup);

        // Act
        $productOptionGroupIds = $this->expandAndFetchProductOptionGroupIds();

        // Assert
        $this->assertSame([$idProductOptionGroup], $productOptionGroupIds);
    }

    public function testExpandProductOptionGroupQueryUsesOnlyIntegerPrefixOfJoinWhereConditionValue(): void
    {
        // Arrange
        $idProductOptionGroup = $this->tester->haveProductOptionGroup()->getIdProductOptionGroupOrFail();
        $this->tester->haveProductOptionGroup();
        $this->setJoinWhereConditionValue(sprintf(static::WHERE_CONDITION_VALUE_TEMPLATE_WITH_SQL_TAIL, $idProductOptionGroup));

        // Act
        $productOptionGroupIds = $this->expandAndFetchProductOptionGroupIds();

        // Assert
        $this->assertSame([$idProductOptionGroup], $productOptionGroupIds);
    }

    protected function setJoinWhereConditionValue(string $value): void
    {
        $queryJoinTransfer = (new QueryJoinTransfer())
            ->setJoinType(Criteria::LEFT_JOIN)
            ->setLeft([SpyProductOptionGroupTableMap::COL_ID_PRODUCT_OPTION_GROUP])
            ->setRight([SpyProductOptionValueTableMap::COL_FK_PRODUCT_OPTION_GROUP])
            ->addQueryWhereCondition(
                (new QueryWhereConditionTransfer())
                    ->setColumn(SpyProductOptionGroupTableMap::COL_ID_PRODUCT_OPTION_GROUP)
                    ->setValue($value),
            );

        $this->tester->setDependency(
            ProductOptionDependencyProvider::PLUGINS_PRODUCT_OPTION_LIST_TABLE_QUERY_CRITERIA_EXPANDER,
            [$this->createQueryJoinExpanderPlugin($queryJoinTransfer)],
        );
    }

    protected function createQueryJoinExpanderPlugin(QueryJoinTransfer $queryJoinTransfer): ProductOptionListTableQueryCriteriaExpanderPluginInterface
    {
        return new class ($queryJoinTransfer) implements ProductOptionListTableQueryCriteriaExpanderPluginInterface {
            public function __construct(protected QueryJoinTransfer $queryJoinTransfer)
            {
            }

            public function expandQueryCriteria(QueryCriteriaTransfer $queryCriteriaTransfer): QueryCriteriaTransfer
            {
                return $queryCriteriaTransfer->addJoin($this->queryJoinTransfer);
            }
        };
    }

    /**
     * @return array<int>
     */
    protected function expandAndFetchProductOptionGroupIds(): array
    {
        return (new ProductOptionRepository())
            ->expandProductOptionGroupQuery(SpyProductOptionGroupQuery::create())
            ->select([SpyProductOptionGroupTableMap::COL_ID_PRODUCT_OPTION_GROUP])
            ->distinct()
            ->find()
            ->getData();
    }
}
