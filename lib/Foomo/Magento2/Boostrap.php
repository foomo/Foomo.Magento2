<?php
/*
 */

namespace Foomo\Magento2;

use Magento\Framework\App\Bootstrap;



class Boostrap
{
	private static $bootstrap;

	private static $appState;


	/**
	 * @return Bootstrap
	 */
	public static function bootstrap() {
		if (!class_exists('\Magento\Framework\App\Bootstrap')) {
			return self::init();
		}
		return self::$bootstrap;
	}


	/**
	 * @return \Magento\Framework\App\Bootstrap
	 */
	private static function init()
	{
		ini_set('max_execution_time', 30 * 60);
		ini_set('memory_limit', '2G');
		require \Foomo\Magento2\Module::getConf()->magentoRootFolder . '/app/bootstrap.php';

		if (is_null(self::$bootstrap)) {
			//handle dockerized setup

			if ($_SERVER['HTTP_HOST'] == 'site') {
				$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
				$_SERVER['HTTPS'] = 'on';
			}
			// add bootstrap

			//create the bootstrap object
			self::$bootstrap = Bootstrap::create(BP, $_SERVER);
			$obj = self::$bootstrap->getObjectManager();
			$state = $obj->get('Magento\Framework\App\State');
			$state->setAreaCode('frontend');
		}
		return self::$bootstrap;
	}

	/**
	 * @return \Magento\Store\Api\Data\StoreInterface[]
	 */
	public static function getStores() {
		if (!class_exists('\Magento\Framework\App\Bootstrap')) {
			self::init();
		}
		/* @var \Magento\Store\Model\StoreManagerInterface $storeManager */
		$storeManager = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Store\Model\StoreManagerInterface');
		return $storeManager->getStores();
	}


	/**
	 * @return \Magento\Store\Api\Data\WebsiteExtensionInterface
	 */
	public static function getWebsites() {
		if (!class_exists('\Magento\Framework\App\Bootstrap')) {
			self::init();
		}
		/* @var \Magento\Store\Model\StoreManagerInterface $storeManager */
		$storeManager = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Store\Model\StoreManagerInterface');
		return $storeManager->getWebsites();
	}

	public static function getStoreCodes() {
		$ret = [];
		foreach (self::getStores() as $store) {
			$ret[] = $store->getCode();
		}
		return $ret;
	}


	public static function getWebsiteCodes() {
		$ret = [];
		foreach (self::getWebsites() as $website) {
			$ret[] = $website->getCode();
		}
		return $ret;
	}
}