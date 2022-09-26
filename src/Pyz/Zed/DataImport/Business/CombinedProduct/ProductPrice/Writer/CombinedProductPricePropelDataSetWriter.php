<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\DataImport\Business\CombinedProduct\ProductPrice\Writer;

use Pyz\Zed\DataImport\Business\CombinedProduct\ProductPrice\CombinedProductPriceHydratorStep;
use Pyz\Zed\DataImport\Business\Model\ProductPrice\Writer\ProductPricePropelDataSetWriter;

class CombinedProductPricePropelDataSetWriter extends ProductPricePropelDataSetWriter
{
    protected const COLUMN_ABSTRACT_SKU = CombinedProductPriceHydratorStep::COLUMN_ABSTRACT_SKU;

    protected const COLUMN_CONCRETE_SKU = CombinedProductPriceHydratorStep::COLUMN_CONCRETE_SKU;

    protected const COLUMN_STORE = CombinedProductPriceHydratorStep::COLUMN_STORE;

    protected const COLUMN_CURRENCY = CombinedProductPriceHydratorStep::COLUMN_CURRENCY;

    protected const COLUMN_PRICE_GROSS = CombinedProductPriceHydratorStep::COLUMN_PRICE_GROSS;

    protected const COLUMN_PRICE_NET = CombinedProductPriceHydratorStep::COLUMN_PRICE_NET;

    protected const COLUMN_PRICE_DATA = CombinedProductPriceHydratorStep::COLUMN_PRICE_DATA;

    protected const COLUMN_PRICE_DATA_CHECKSUM = CombinedProductPriceHydratorStep::COLUMN_PRICE_DATA_CHECKSUM;
}
