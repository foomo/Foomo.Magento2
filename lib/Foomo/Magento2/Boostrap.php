<?php
/*
 */

namespace Foomo\Magento2;
use Magento\Framework\App\Bootstrap;

require \Foomo\Magento2\Module::getModuleConfig()->magentoRootFolder . '/app/bootstrap.php';

class Boostrap
{
	private static $bootsrap;

	private static $appState;


	/**
	 * @return \Magento\Framework\App\Bootstrap
	 */
	public static function init() {
		if (is_null(self::$bootstrap)) {
			self::$bootstrap = Bootstrap::create(BP, $_SERVER);
			$obj = self::$bootstrap->getObjectManager();
			$state = $obj->get('Magento\Framework\App\State');
			$state->setAreaCode('frontend');
		}
		return self::$bootsrap;
	}

}