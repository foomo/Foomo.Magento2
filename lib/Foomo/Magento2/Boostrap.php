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
	 * @return \Magento\Framework\App\Bootstrap
	 */
	public static function bootstrap()
	{
		
		if (!class_exists('\Magento\Framework\App\Bootstrap')) {
			return self::init();
		} else if(!self::$bootstrap) {
			self::$bootstrap = Bootstrap::create(BP, $_SERVER);
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
				$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
			}
//let us pretend its https - > we are in a private net
			$_SERVER['HTTPS'] = 'on';
			$_SERVER['HTTP_X_FORWARDED_PROTO'] = 'https';
			$https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http';
			$_SERVER['REQUEST_SCHEME'] = isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : $https;

			//create the bootstrap object
			self::$bootstrap = Bootstrap::create(BP, $_SERVER);
			$obj = self::$bootstrap->getObjectManager();
			/* @var \Magento\Framework\App\State $state */
			$state = $obj->get('Magento\Framework\App\State');
			$state->setAreaCode('frontend');
		}
		return self::$bootstrap;
	}

	/**
	 * @return \Magento\Store\Api\Data\StoreInterface[]
	 */
	public static function getObjectManager()
	{
		if (!class_exists('\Magento\Framework\App\Bootstrap')) {
			self::init();
		} else {
		}
		return \Magento\Framework\App\ObjectManager::getInstance();

	}

	/**
	 * @return \Magento\Store\Api\Data\StoreInterface[]
	 */
	public static function getStores()
	{
		if (!class_exists('\Magento\Framework\App\Bootstrap')) {
			self::init();
		}
		/* @var \Magento\Store\Model\StoreManagerInterface $storeManager */
		$storeManager = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Store\Model\StoreManagerInterface');
		return $storeManager->getStores();
	}

	/**
	 * @param string $storeCode
	 * @return \Magento\Store\Api\Data\StoreInterface | false
	 */
	public static function getStoreForCode($storeCode)
	{
		if (!class_exists('\Magento\Framework\App\Bootstrap')) {
			self::init();
		}
		/* @var \Magento\Store\Model\StoreManagerInterface $storeManager */
		$storeManager = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Store\Model\StoreManagerInterface');
		foreach ($storeManager->getStores() as $store) {
			if ($store->getCode() == $storeCode) {
				return $store;
			}
		}
		return false;
	}


	/**
	 * @param string $storeId
	 * @return \Magento\Store\Api\Data\StoreInterface | false
	 */
	public static function getStoreById($storeId)
	{
		if (!class_exists('\Magento\Framework\App\Bootstrap')) {
			self::init();
		}
		/* @var \Magento\Store\Model\StoreManagerInterface $storeManager */
		$storeManager = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Store\Model\StoreManagerInterface');
		foreach ($storeManager->getStores() as $store) {
			if ($store->getId() == $storeId) {
				return $store;
			}
		}
		return false;
	}


	/**
	 * set current store
	 * @param string $storeCode
	 */
	public static function setCurrentStore($storeCode)
	{
		if (!class_exists('\Magento\Framework\App\Bootstrap')) {
			self::init();
		}
		/* @var \Magento\Store\Model\StoreManagerInterface $storeManager */
		$storeManager = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Store\Model\StoreManagerInterface');
		$store = self::getStoreForCode($storeCode);
		return $storeManager->setCurrentStore(!empty($store) ? $store->getId() : 0); // default to admin store
	}


	/**
	 * set current store
	 * @return string \Magento\Store\Api\Data\StoreInterface | false
	 */
	public static function getCurrentStore()
	{
		if (!class_exists('\Magento\Framework\App\Bootstrap')) {
			self::init();
		}
		$storeManager = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Store\Model\StoreManagerInterface');
		return $storeManager->getStore();


	}

	/**
	 * @return \Magento\Store\Api\Data\WebsiteExtensionInterface
	 */
	public static function getWebsites()
	{
		if (!class_exists('\Magento\Framework\App\Bootstrap')) {
			self::init();
		}
		/* @var \Magento\Store\Model\StoreManagerInterface $storeManager */
		$storeManager = \Magento\Framework\App\ObjectManager::getInstance()->get('\Magento\Store\Model\StoreManagerInterface');
		return $storeManager->getWebsites();
	}

	public static function getStoreCodes()
	{
		$ret = [];
		foreach (self::getStores() as $store) {
			$ret[] = $store->getCode();
		}
		return $ret;
	}


	public static function getWebsiteCodes()
	{
		$ret = [];
		foreach (self::getWebsites() as $website) {
			$ret[] = $website->getCode();
		}
		return $ret;
	}


	/**
	 * get country code to country for current store
	 * @param string $countryCode
	 * @return string
	 */
	public static function getCountryCodeTranslation($countryCode)
	{
		/* @var \Magento\Framework\Locale\ListsInterface $listInterface */
		$listInterface = self::bootstrap()->getObjectManager()->get('\Magento\Framework\Locale\ListsInterface');
		return $listInterface->getCountryTranslation($countryCode);
	}
}