<?php
/*
 */

namespace Foomo\Magento2;

class Indexer
{

	public static function reindex($indexerIds = [])
	{
		$bootstrap = Boostrap::bootstrap();
		$indexerFactory = $bootstrap->getObjectManager()->get('Magento\Indexer\Model\IndexerFactory');

		if (empty($indexerIds)) {
			$indexerIds = array(
				'catalog_category_product',
				'catalog_product_category',
				'catalog_product_price',
				'catalog_product_attribute',
				'cataloginventory_stock',
				'catalogrule_product',
				'catalogsearch_fulltext',
			);
		}

		foreach ($indexerIds as $indexerId) {

			$indexer = $indexerFactory->create();
			$indexer->load($indexerId);
			$indexer->reindexAll();
			\Foomo\Utils::appendToPhpErrorLog('REINDEX: ' . $indexerId . ' done.' . PHP_EOL);
		}
	}


}