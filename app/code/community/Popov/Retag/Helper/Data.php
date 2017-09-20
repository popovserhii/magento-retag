<?php
/**
 * Admitad ReTag default Helper
 *
 * @category Popov
 * @package Popov_Retag
 * @author Popov Sergiy <popow.serhii@gmail.com>
 * @datetime: 20.04.14 14:54
 */
class Popov_Retag_Helper_Data extends Mage_Core_Helper_Abstract
{
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

    public function getModuleCode($name)
    {
        $cache = [];
        if (isset($cache[$name])) {
            return $cache[$name];
        }

        $code = [];
        $parts = explode('_', $name);
        foreach ($parts as $part) {
            $code[] = lcfirst($part);
        }

        return $cache[$name] = implode('_', $code);
    }

    public function isModuleEnable($name)
    {
        return Mage::getStoreConfig($this->getModuleCode($name) . '/settings/enabled');
    }

    public function setCookies()
    {
        /** @var Popov_Retag_Helper_Data $helper */
        $modulesConfig = $this->getRetargetingConfig()['modules'];
        foreach ($modulesConfig as $name => $config) {
            $moduleCode = $this->getModuleCode($name);
            if ($this->isModuleEnabled($name) && Mage::getStoreConfig($moduleCode . '/settings/postback_url')) {
                $helper = Mage::helper($moduleCode);
                $helper->setCookies();
            }
        }
    }

    public function clearCookies()
    {
        $request = Mage::app()->getRequest();
        foreach ($this->getRetargetingConfig()['modules'] as $name => $config) {
            if ($request->get($utmUidName = $config['utm_uid_name'])) {
                foreach ($this->getRetargetingConfig()['modules'] as $_name => $_config) {
                    if (($utmUidName !== $config['utm_uid_name']) && $this->isModuleEnabled($name)) {
                        $helper = Mage::helper($this->getModuleCode($_name));
                        $helper->clearCookies();
                    }
                }
            }
        }
    }

    public function setScript()
    {
        $modulesConfig = $this->getRetargetingConfig()['modules'];
        foreach ($modulesConfig as $name => $config) {
            if (!$this->isModuleEnabled($name)) {
                continue;
            }
            $moduleCode = $this->getModuleCode($name);
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
    }

    public function send()
    {
        $modulesConfig = $this->getRetargetingConfig()['modules'];
        foreach ($modulesConfig as $name => $config) {
            $noduleHelper = Mage::helper(strtolower($name) . '/postBack');
            $noduleHelper->send();
        }
    }
}
