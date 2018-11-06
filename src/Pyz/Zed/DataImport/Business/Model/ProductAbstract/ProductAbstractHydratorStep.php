<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\DataImport\Business\Model\ProductAbstract;

use Generated\Shared\Transfer\SpyProductAbstractEntityTransfer;
use Generated\Shared\Transfer\SpyProductAbstractLocalizedAttributesEntityTransfer;
use Generated\Shared\Transfer\SpyProductCategoryEntityTransfer;
use Generated\Shared\Transfer\SpyUrlEntityTransfer;
use Pyz\Zed\DataImport\Business\Exception\InvalidSkuProductException;
use Pyz\Zed\DataImport\Business\Model\Product\ProductLocalizedAttributesExtractorStep;
use Pyz\Zed\DataImport\Business\Model\Product\Repository\ProductRepository;
use Spryker\Zed\DataImport\Business\Exception\DataKeyNotFoundInDataSetException;
use Spryker\Zed\DataImport\Business\Model\DataImportStep\DataImportStepInterface;
use Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface;

class ProductAbstractHydratorStep implements DataImportStepInterface
{
    public const BULK_SIZE = 5000;
    public const DATA_PRODUCT_ABSTRACT_TRANSFER = 'DATA_PRODUCT_ABSTRACT_TRANSFER';
    public const DATA_PRODUCT_ABSTRACT_LOCALIZED_TRANSFER = 'DATA_PRODUCT_ABSTRACT_LOCALIZED_TRANSFER';
    public const DATA_PRODUCT_CATEGORY_TRANSFER = 'DATA_PRODUCT_CATEGORY_TRANSFER';
    public const DATA_PRODUCT_URL_TRANSFER = 'DATA_PRODUCT_URL_TRANSFER';
    public const KEY_PRODUCT_CATEGORY_TRANSFER = 'productCategoryTransfer';
    public const KEY_PRODUCT_ABSTRACT_LOCALIZED_TRANSFER = 'localizedAttributeTransfer';
    public const KEY_PRODUCT_URL_TRASNFER = 'urlTransfer';
    public const KEY_ABSTRACT_SKU = 'abstract_sku';
    public const KEY_SKU = 'sku';
    public const KEY_COLOR_CODE = 'color_code';
    public const KEY_ID_TAX_SET = 'idTaxSet';
    public const KEY_FK_TAX_SET = 'fk_tax_set';
    public const KEY_ATTRIBUTES = 'attributes';
    public const KEY_NAME = 'name';
    public const KEY_URL = 'url';
    public const KEY_DESCRIPTION = 'description';
    public const KEY_META_TITLE = 'meta_title';
    public const KEY_META_DESCRIPTION = 'meta_description';
    public const KEY_META_KEYWORDS = 'meta_keywords';
    public const KEY_TAX_SET_NAME = 'tax_set_name';
    public const KEY_CATEGORY_KEY = 'category_key';
    public const KEY_CATEGORY_KEYS = 'categoryKeys';
    public const KEY_FK_CATEGORY = 'fk_category';
    public const KEY_CATEGORY_PRODUCT_ORDER = 'category_product_order';
    public const KEY_PRODUCT_ORDER = 'product_order';
    public const KEY_LOCALES = 'locales';
    public const KEY_FK_LOCALE = 'fk_locale';
    public const KEY_NEW_FROM = 'new_from';
    public const KEY_NEW_TO = 'new_to';
    public const KEY_ID_URL = 'id_url';
    public const KEY_ID_PRODUCT_ABSTRACT = 'id_product_abstract';

    /**
     * @var \Pyz\Zed\DataImport\Business\Model\Product\Repository\ProductRepository
     */
    protected $productRepository;

    /**
     * @var array Keys are concrete product sku values.
     */
    protected static $skuProductConcreteList = [];

    /**
     * @var array Keys are abstract product sku values. Values are set to "true" when abstract product added.
     */
    protected static $resolved = [];

    /**
     * @param \Pyz\Zed\DataImport\Business\Model\Product\Repository\ProductRepository $productRepository
     */
    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;

        static::$skuProductConcreteList = array_flip($productRepository->getSkuProductConcreteList());
    }

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    public function execute(DataSetInterface $dataSet): void
    {
        $this->checkSkuProductAlreadyExists($dataSet);
        $this->importProductAbstract($dataSet);
        $this->importProductAbstractLocalizedAttributes($dataSet);
        $this->importProductCategories($dataSet);
        $this->importProductUrls($dataSet);
    }

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @throws \Pyz\Zed\DataImport\Business\Exception\InvalidSkuProductException
     *
     * @return void
     */
    protected function checkSkuProductAlreadyExists(DataSetInterface $dataSet): void
    {
        $sku = $dataSet[static::KEY_ABSTRACT_SKU];

        if (isset(static::$skuProductConcreteList[$sku])) {
            throw new InvalidSkuProductException(sprintf('Concrete product with SKU "%s" already exists.', $sku));
        }

        if (isset(static::$resolved[$sku])) {
            throw new InvalidSkuProductException(sprintf('Abstract product with SKU "%s" has been already imported.', $sku));
        }

        static::$resolved[$sku] = true;
    }

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    protected function importProductAbstract(DataSetInterface $dataSet): void
    {
        $productAbstractEntityTransfer = new SpyProductAbstractEntityTransfer();
        $productAbstractEntityTransfer->setSku($dataSet[static::KEY_ABSTRACT_SKU]);

        $productAbstractEntityTransfer
            ->setColorCode($dataSet[static::KEY_COLOR_CODE])
            ->setFkTaxSet($dataSet[static::KEY_ID_TAX_SET])
            ->setAttributes(json_encode($dataSet[static::KEY_ATTRIBUTES]))
            ->setNewFrom($dataSet[static::KEY_NEW_FROM])
            ->setNewTo($dataSet[static::KEY_NEW_TO]);

        $dataSet[static::DATA_PRODUCT_ABSTRACT_TRANSFER] = $productAbstractEntityTransfer;
    }

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    protected function importProductAbstractLocalizedAttributes(DataSetInterface $dataSet): void
    {
        $localizedAttributeTransfer = [];

        foreach ($dataSet[ProductLocalizedAttributesExtractorStep::KEY_LOCALIZED_ATTRIBUTES] as $idLocale => $localizedAttributes) {
            $productAbstractLocalizedAttributesEntityTransfer = new SpyProductAbstractLocalizedAttributesEntityTransfer();
            $productAbstractLocalizedAttributesEntityTransfer
                ->setName($localizedAttributes[static::KEY_NAME])
                ->setDescription($localizedAttributes[static::KEY_DESCRIPTION])
                ->setMetaTitle($localizedAttributes[static::KEY_META_TITLE])
                ->setMetaDescription($localizedAttributes[static::KEY_META_DESCRIPTION])
                ->setMetaKeywords($localizedAttributes[static::KEY_META_KEYWORDS])
                ->setFkLocale($idLocale)
                ->setAttributes(json_encode($localizedAttributes[static::KEY_ATTRIBUTES]));

            $localizedAttributeTransfer[] = [
                static::KEY_ABSTRACT_SKU => $dataSet[static::KEY_ABSTRACT_SKU],
                static::KEY_PRODUCT_ABSTRACT_LOCALIZED_TRANSFER => $productAbstractLocalizedAttributesEntityTransfer,
            ];
        }

        $dataSet[static::DATA_PRODUCT_ABSTRACT_LOCALIZED_TRANSFER] = $localizedAttributeTransfer;
    }

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @throws \Spryker\Zed\DataImport\Business\Exception\DataKeyNotFoundInDataSetException
     *
     * @return void
     */
    protected function importProductCategories(DataSetInterface $dataSet): void
    {
        $productCategoryTransfers = [];
        $categoryKeys = $this->getCategoryKeys($dataSet[static::KEY_CATEGORY_KEY]);
        $categoryProductOrder = $this->getCategoryProductOrder($dataSet[static::KEY_CATEGORY_PRODUCT_ORDER]);

        foreach ($categoryKeys as $index => $categoryKey) {
            if (!isset($dataSet[static::KEY_CATEGORY_KEYS][$categoryKey])) {
                throw new DataKeyNotFoundInDataSetException(sprintf(
                    'The category with key "%s" was not found in categoryKeys. Maybe there is a typo. Given Categories: "%s"',
                    $categoryKey,
                    implode(array_values($dataSet[static::KEY_CATEGORY_KEYS]))
                ));
            }

            $productOrder = 0;

            if (count($categoryProductOrder) && isset($categoryProductOrder[$index])) {
                $productOrder = (int)$categoryProductOrder[$index];
            }

            $productCategoryEntityTransfer = new SpyProductCategoryEntityTransfer();
            $productCategoryEntityTransfer
                ->setFkCategory($dataSet[static::KEY_CATEGORY_KEYS][$categoryKey])
                ->setProductOrder($productOrder);

            $productCategoryTransfers[] = [
                static::KEY_ABSTRACT_SKU => $dataSet[static::KEY_ABSTRACT_SKU],
                static::KEY_PRODUCT_CATEGORY_TRANSFER => $productCategoryEntityTransfer,
            ];
        }

        $dataSet[static::DATA_PRODUCT_CATEGORY_TRANSFER] = $productCategoryTransfers;
    }

    /**
     * @param \Spryker\Zed\DataImport\Business\Model\DataSet\DataSetInterface $dataSet
     *
     * @return void
     */
    protected function importProductUrls(DataSetInterface $dataSet): void
    {

        $urlsTransfer = [];

        foreach ($dataSet[ProductLocalizedAttributesExtractorStep::KEY_LOCALIZED_ATTRIBUTES] as $idLocale => $localizedAttributes) {
            $abstractProductUrl = $localizedAttributes[static::KEY_URL];

            $urlEntityTransfer = new SpyUrlEntityTransfer();

            $urlEntityTransfer
                ->setFkLocale($idLocale)
                ->setUrl($abstractProductUrl);

            $urlsTransfer[] = [
                static::KEY_ABSTRACT_SKU => $dataSet[static::KEY_ABSTRACT_SKU],
                static::KEY_PRODUCT_URL_TRASNFER => $urlEntityTransfer,
            ];
        }

        $dataSet[static::DATA_PRODUCT_URL_TRANSFER] = $urlsTransfer;
    }

    /**
     * @param string $categoryKeys
     *
     * @return array
     */
    protected function getCategoryKeys($categoryKeys): array
    {
        $categoryKeys = explode(',', $categoryKeys);

        return array_map('trim', $categoryKeys);
    }

    /**
     * @param string $categoryProductOrder
     *
     * @return array
     */
    protected function getCategoryProductOrder($categoryProductOrder): array
    {
        $categoryProductOrder = explode(',', $categoryProductOrder);

        return array_map('trim', $categoryProductOrder);
    }
}
