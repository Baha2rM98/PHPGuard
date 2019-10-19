<?php

/**
 * @author Baha2r
 * @license MIT
 * Date: 18/Oct/2019
 *
 * This trait find absolute path of assets directory
 **/

namespace PHPGuard\Core;


abstract class AssetPath
{

    /**
     * Find absolute path of assets directory
     * @return string returns absolute path of assets directory
     */
    protected function getAbsolutePath()
    {
        return __DIR__;
    }
}