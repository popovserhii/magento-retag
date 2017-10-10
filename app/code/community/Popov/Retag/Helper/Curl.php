<?php

/**
 * Enter description here...
 *
 * @category Popov
 * @package Popov_Retag
 * @author Popov Sergiy <popow.serhii@gmail.com>
 * @datetime: 07.06.2017 17:48
 */
class Popov_Retag_Helper_Curl extends Mage_Core_Helper_Abstract
{
    public function send($url, $params)
    {
        $response = null;
        if (isset($params[0])) {
            foreach ($params as $post) {
                $response[] = $this->send($url, $post);
            }
        } else {
            $response = $this->curl($url, $params);
        }

        return $response;
    }

    public function curl($url, $data)
    {
        $ch = curl_init();

        try {
            $urlQuery = $url . '?' . http_build_query($data);
            // Set query data here with the URL
            curl_setopt($ch, CURLOPT_URL, $urlQuery);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            #curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            $response = curl_exec($ch);

            if (false === $response) {
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
            curl_close($ch);

            return $response;

        } catch (Exception $e) {
            curl_close($ch);
            Mage::logException($e);
        }

        return false;
    }
}