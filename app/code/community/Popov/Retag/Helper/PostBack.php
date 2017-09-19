<?php

/**
 * Enter description here...
 *
 * @category Popov
 * @package Popov_Retag
 * @author Popov Sergiy <popow.serhii@gmail.com>
 * @datetime: 07.06.2017 17:48
 */
class Popov_Retag_Helper_PostBack
{
    public function send($url, $data)
    {
        $urlQuery = $url . '?' . http_build_query($data);
        $ch = curl_init();
        // Set query data here with the URL
        curl_setopt($ch, CURLOPT_URL, $urlQuery);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        #curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}