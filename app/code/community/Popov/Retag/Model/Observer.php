<?php
/**
 * Add Admitad ReTag
 *
 * @category Popov
 * @package Popov_Retag
 * @author Popov Sergiy <popow.serhii@gmail.com>
 * @datetime: 24.05.2017 17:40
 */
class Popov_Retag_Model_Observer extends Varien_Event_Observer
{
	/**
	 * @var Popov_Retag_Helper_Data $helper
	 */
	protected $helper;

 	public function hookToSetScript()
	{
        if (!Mage::app()->getStore()->isAdmin()) {
            $this->getHelper()->setScript();
        }
	}

    public function hookToSendBackRequest()
	{
        if (!Mage::app()->getStore()->isAdmin()) {
            $this->getHelper()->send();
        }
    }

    public function hookToSetCookies()
    {
        if (!Mage::app()->getStore()->isAdmin()) {
            $this->getHelper()->setCookies();
            $this->getHelper()->clearCookies();
        }
    }

    public function getHelper() {
        if (!$this->helper) {
            $this->helper = Mage::helper('popov_retag');
        }

        return $this->helper;
    }

}