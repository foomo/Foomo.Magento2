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
			$indexerIds = self::getIndexerIds();
		}
		foreach ($indexerIds as $indexerId) {
			$indexer = $indexerFactory->create();
			$indexer->load($indexerId);
			$indexer->reindexAll();
			\Foomo\Utils::appendToPhpErrorLog('REINDEX: ' . $indexerId . ' done.' . PHP_EOL);
		}
	}

	public static function isIndexerRunning()
	{
		$bootstrap = Boostrap::bootstrap();
		$indexerFactory = $bootstrap->getObjectManager()->get('Magento\Indexer\Model\IndexerFactory');
		$indexerIds = self::getIndexerIds();
		foreach ($indexerIds as $indexerId) {
			/** @var \Magento\Indexer\Model\Indexer $indexer */
			$indexer = $indexerFactory->create();
			$indexer->load($indexerId);
			if ($indexer->getStatus() == \Magento\Framework\Indexer\StateInterface::STATUS_WORKING) {
				return true;
			}
		}
		return false;
	}

	/**
	 * get running indexer codes
	 *
	 * @return string[]
	 */
	public static function getRunningIndexers()
	{
		$indexerIds = [];
		$bootstrap = Boostrap::bootstrap();
		//CacheCleaner::cleanAll();
		/** @var \Magento\Indexer\Model\Indexer\Collection $indexerCollection */
		$indexerCollection = $bootstrap->getObjectManager()->get(\Magento\Indexer\Model\Indexer\Collection::class);
		$indexerCollection->load();
		/** @var \Magento\Indexer\Model\Indexer $indexer */
		foreach ($indexerCollection->getItems() as $indexer) {
			//$indexer->reindexAll();
			if ($indexer->getStatus() == \Magento\Framework\Indexer\StateInterface::STATUS_WORKING) {
				$indexerIds[] = $indexer->getId();
			}
		}
		return $indexerIds;
	}

	private static function getIndexerIds()
	{
		$indexerIds = [];
		$bootstrap = Boostrap::bootstrap();
		//CacheCleaner::cleanAll();
		/** @var \Magento\Indexer\Model\Indexer\Collection $indexerCollection */
		$indexerCollection = $bootstrap->getObjectManager()->get(\Magento\Indexer\Model\Indexer\Collection::class);
		$indexerCollection->load();
		/** @var \Magento\Indexer\Model\Indexer $indexer */
		foreach ($indexerCollection->getItems() as $indexer) {
			//$indexer->reindexAll();
			$indexerIds[] = $indexer->getId();
		}
		return $indexerIds;
	}
}