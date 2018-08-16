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
        if (!$this->retargetingConfig) {
            $this->retargetingConfig = Mage::getConfig()->getNode('retargeting')->asArray();
        }

        return $this->retargetingConfig;

    }

    public function getModuleCode($name)
    {
        static $cache = [];
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

    public function isRetagEnabled($name)
    {
        return Mage::getStoreConfig($this->getModuleCode($name) . '/settings/enabled');
    }

    public function getRetagName($name)
    {
        return explode('_', $name)[1];
    }

    public function isLastCookieWins($name)
    {
        $config = $this->getRetargetingConfig();
        $cookie = Mage::getSingleton('core/cookie');

        $retagName = strtoupper($this->getRetagName($name));
        $cookieName = isset($config['modules'][$name]['utm_uid_name'])
            ? strtoupper($config['modules'][$name]['utm_uid_name'])
            : null;
        if (strpos($cookieName, $retagName) === false) {
            $cookieName = strtoupper($this->getRetagName($name) . '_' . $config['modules'][$name]['utm_uid_name']);
        }

        if ($cookie->get($cookieName)) {
            return true;
        }

        return false;
    }

    public function setCookies()
    {
        $modulesConfig = $this->getRetargetingConfig()['modules'];
        foreach ($modulesConfig as $name => $config) {
            if ($this->isRetagEnabled($name)) {
                $moduleCode = $this->getModuleCode($name);
                $helper = Mage::helper($moduleCode);
                if (method_exists($helper, 'setCookies')) {
                    $helper->setCookies();
                }
            }
        }
    }

    public function clearCookies()
    {
        $request = Mage::app()->getRequest();
        foreach ($this->getRetargetingConfig()['modules'] as $name => $config) {
            if (isset( $config['utm_uid_name']) && $request->get($utmUidName = $config['utm_uid_name'])) {
                foreach ($this->getRetargetingConfig()['modules'] as $_name => $_config) {
                    if (($utmUidName !== $_config['utm_uid_name'])/* && $this->isRetagEnabled($name)*/) {
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
            if (!$this->isRetagEnabled($name)) {
                continue;
            }

            $moduleCode = $this->getModuleCode($name);
            $blockCode = $moduleCode . '/script';
            $blockClass = Mage::getConfig()->getBlockClassName($blockCode);
            if (!class_exists($blockClass)) {
                continue;
            }

            $blockName = str_replace('_', '.', $moduleCode) . '.retag.script';
            $block = Mage::app()->getLayout()->createBlock(
                $blockCode,
                $blockName,
                ['action' => Mage::app()->getFrontController()->getAction()->getFullActionName()]
            );
            $beforeBodyEnd = Mage::app()->getLayout()->getBlock('before_body_end');
            $beforeBodyEnd->append($block);
        }
    }

    public function send()
    {
        $modulesConfig = $this->getRetargetingConfig()['modules'];
        foreach ($modulesConfig as $name => $config) {
            if (!$this->isRetagEnabled($name) || !$this->isLastCookieWins($name)) {
                continue;
            }

            /** @var Popov_Retag_Helper_PostBackInterface $postBackHelper */
            $postBackHelper = Mage::helper($this->getModuleCode($name) . '/postBack');
            /** @var Popov_Retag_Helper_Curl $curlHelper */
            $curlHelper = Mage::helper('popov_retag/curl');

            $curlHelper->send($postBackHelper->getUrl(), $postBackHelper->getParams());
        }
    }
}
