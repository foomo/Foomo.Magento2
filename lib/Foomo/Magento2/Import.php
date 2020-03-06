<?php

namespace Foomo\Magento2;

use BigBridge\ProductImport\Api\Data\SimpleProduct;
use BigBridge\ProductImport\Api\ImportConfig;
use BigBridge\ProductImport\Api\ImporterFactory;
use Kennys\Services\Log;
use Magento\Framework\App\ObjectManager;

class Import
{
	/**
	 * @var \BigBridge\ProductImport\Api\Importer
	 */
	private $importer = null;

	public function __construct()
	{
		$this->importer = self::init();
	}


	public function commit() {
		//process an remaining in the pipeline
		$this->importer->flush();
	}

	/**
	 * @param \BigBridge\ProductImport\Api\Data\Product $product
	 */
	public function import($product) {
		$this->importer->importSimpleProduct($product);
	}

	/**
	 * @param \BigBridge\ProductImport\Api\Data\ConfigurableProduct $product
	 */
	public function importConfigurableProduct($product) {
		$this->importer->importConfigurableProduct($product);
	}


	/**
	 * @return
	 */
	private static function init() {
		\Foomo\Magento2\Boostrap::bootstrap();
		$config = self::getImporterConfig();
		// a callback function to postprocess imported products \BigBridge\ProductImport\Api\Data\Product
		$config->resultCallback = function( $product) use (&$log) {

			/** @var \BigBridge\ProductImport\Api\Data\Product $product **/
			if ($product->isOk()) {
				$log .= $product->getSku() . ' magento upsert';
			} else {
				$log .= sprintf("%s: failed! error = %s\n", $product->getSku(), implode('; ', $product->getErrors()));
			}
		};

		$factory = ObjectManager::getInstance()->get(ImporterFactory::class);
		return $factory->createImporter(self::getImporterConfig());
	}


	/**
	 * @return \BigBridge\ProductImport\Api\ImportConfig
	 */
	private static function getImporterConfig() {
		$config = new ImportConfig();
		$config->autoCreateCategories = true;
		$config->autoCreateOptionAttributes = ['size'];
		$config->batchSize = 5000;
		$config->duplicateUrlKeyStrategy = ImportConfig::DUPLICATE_KEY_STRATEGY_ADD_SERIAL;

		$config->resultCallback = function($product) use (&$log) {

			if ($product->isOk()) {
				$log = sprintf("%s: success! sku = %s, id = %s\n", $product->lineNumber, $product->getSku(), $product->id);
			} else {
				$log = sprintf("%s: failed! error = %s\n", $product->lineNumber, implode('; ', $product->getErrors()));
				\Foomo\Utils::appendToPhpErrorLog('**************' . $log . PHP_EOL);
			}

		};
		return $config;
	}




}