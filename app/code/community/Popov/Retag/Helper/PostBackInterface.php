<?php

/**
 * The MIT License (MIT)
 * Copyright (c) 2017 Serhii Popov
 * This source file is subject to The MIT License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/MIT
 *
 * @category Popov
 * @package Popov_Retag
 * @author Serhii Popov <popow.sergiy@gmail.com>
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */
interface Popov_Retag_Helper_PostBackInterface
{
    /**
     * Get Post Back URL
     *
     * @return string
     */
    public function getUrl();

    /**
     * Get params for Post Back request
     *
     * Can be multidimensional array
     *
     * @return array
     */
    public function getParams();
}