<?php
/*
 */

namespace Foomo\Magento2\Services;

class Cart
{
	/**
	 * quote
	 *
	 * @param \Magento\Quote\Model\Quote
	 *
	 */
	private static $quote = null;

	/**
	 * @param string $storeCode
	 * @return \Magento\Quote\Model\Quote
	 * @throws \Magento\Framework\Exception\SessionException
	 */
	public static function getQuote($storeCode)
	{
		if (!self::$quote) {
			// do not touch magento calls !
			$bootstrap = \Foomo\Magento2\Boostrap::bootstrap();
			\Foomo\Magento2\Boostrap::setCurrentStore($storeCode);
			$objectManager = $bootstrap->getObjectManager();
			$quote = $objectManager->get('Magento\Checkout\Model\Session')->getQuote();
			$country = 'DE';
			if ($storeCode != 'admin' && $storeCode != 'base') {
				$country = strtoupper(substr($storeCode, 0, 2));
			}

			if (!$quote->hasItems()) {
				$quote->setBillingAddress(self::getEmptyAddress($country, $storeCode))->setShippingAddress(self::getEmptyAddress($country, $storeCode));
			}
			self::$quote = $quote;
		}
		return self::$quote;

	}

	private static function getEmptyAddress($country, $storeCode)
	{
		$bootstrap = \Foomo\Magento2\Boostrap::bootstrap();
		\Foomo\Magento2\Boostrap::setCurrentStore($storeCode);
		$objectManager = $bootstrap->getObjectManager();
		$address = $objectManager->create('Magento\Quote\Api\Data\AddressInterface');
		$address->setRegionId(0);
		$address->setRegion("");
		$address->setRegionCode("");
		$address->setCountryId($country);
		$address->setStreet([""]);
		$address->setCompany("");
		$address->setTelephone("");
		$address->setFax("");
		$address->setPostcode("");
		$address->setCity("");
		$address->setFirstname("");
		$address->setLastname("");
		$address->setMiddlename("");
		$address->setEmail("");
		$address->setSameAsBilling(1);
		$address->setSaveInAddressBook(0);
		return $address;
	}


	/**
	 * @param string $storeCode
	 * @return \Magento\Checkout\Model\Session
	 */
	public static function getCheckoutSession($storeCode) {
		$bootstrap = \Foomo\Magento2\Boostrap::bootstrap();
		\Foomo\Magento2\Boostrap::setCurrentStore($storeCode);
		$objectManager = \Foomo\Magento2\Boostrap::bootstrap()->getObjectManager();
		return $objectManager->get('Magento\Checkout\Model\Session');
	}
}