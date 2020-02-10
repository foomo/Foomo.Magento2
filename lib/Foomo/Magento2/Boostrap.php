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
			//handle dockerized setup

			if ($_SERVER['HTTP_HOST'] == 'site') {
				$_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
				$_SERVER['HTTPS'] = 'on';
			}

			//create the bootstrap object
			self::$bootstrap = Bootstrap::create(BP, $_SERVER);
			$obj = self::$bootstrap->getObjectManager();
			$state = $obj->get('Magento\Framework\App\State');
			$state->setAreaCode('frontend');
		}
		return self::$bootsrap;
	}

}