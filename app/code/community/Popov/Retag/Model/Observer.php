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

    /**
     * @var array
     */
	protected $retargetingConfig;

	public function getRetargetingConfig()
    {
        if ($this->retargetingConfig) {
            $this->retargetingConfig = Mage::getConfig()->getNode('retargeting')->asArray();
        }

        return $this->retargetingConfig;

    }

	public function hookToSetScript()
	{
        $modulesConfig = $this->getRetargetingConfig()['modules'];
        foreach ($modulesConfig as $name => $config) {
            $moduleCode = strtolower($name);

            $blockCode = $moduleCode . '/script';
            $blockName = str_replace('_', '.', $moduleCode) . '.retag.script';
            $block = Mage::app()->getLayout()->createBlock(
                $blockCode,
                $blockName,
                array('action' => Mage::app()->getFrontController()->getAction()->getFullActionName())
            );
            $beforeBodyEnd = Mage::app()->getLayout()->getBlock('before_body_end');
            $beforeBodyEnd->append($block);
        }

        /*$block = Mage::app()->getLayout()->createBlock(
            'popov_retag/script',
            'admitad.retag.script',
            array('action' => Mage::app()->getFrontController()->getAction()->getFullActionName())
        );
        $beforeBodyEnd = Mage::app()->getLayout()->getBlock('before_body_end');
        $beforeBodyEnd->append($block);*/
	}

    public function hookToSendBackRequest()
	{
        /** @var $noduleHelper Popov_Retag_Helper_PostBack */
        $modulesConfig = $this->getRetargetingConfig()['modules'];
        foreach ($modulesConfig as $name => $config) {
            $noduleHelper = Mage::helper(strtolower($name) . '/postBack');
            $noduleHelper->send();
        }
    }

    public function hookToSetCookies()
    {
        /** @var Popov_Retag_Helper_Data $helper */
        $modulesConfig = $this->getRetargetingConfig()['modules'];
        foreach ($modulesConfig as $name => $config) {
            $helper = Mage::helper(strtolower($name));
            $helper->setCookies();
        }
    }

    public function getHelper() {
        if (!$this->helper) {
            $this->helper = Mage::helper('popov_retag');
        }

        return $this->helper;
    }

}