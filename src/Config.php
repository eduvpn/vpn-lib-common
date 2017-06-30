<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common;

use SURFnet\VPN\Common\Exception\ConfigException;

class Config
{
    /** @var array */
    protected $configData;

    public function __construct(array $configData)
    {
        $this->configData = $configData;
        $this->configData = array_merge(static::defaultConfig(), $configData);
    }

    public static function defaultConfig()
    {
        return [];
    }

    public function hasSection($key)
    {
        if (!array_key_exists($key, $this->configData)) {
            throw new ConfigException(sprintf('section "%s" not available', $key));
        }

        return is_array($this->configData[$key]);
    }

    public function getSection($key)
    {
        if (false === $this->hasSection($key)) {
            throw new ConfigException(sprintf('"%s" is not a section', $key));
        }

        // do not return the parent object if we were subclassed, but an actual
        // "Config" object to avoid copying in the defaults if set
        return new self($this->configData[$key]);
    }

    public function hasItem($key)
    {
        return array_key_exists($key, $this->configData);
    }

    public function getItem($key)
    {
        if (false === $this->hasItem($key)) {
            throw new ConfigException(sprintf('item "%s" not available', $key));
        }

        return $this->configData[$key];
    }

    public static function fromFile($configFile)
    {
        if (false === @file_exists($configFile)) {
            throw new ConfigException(sprintf('unable to read "%s"', $configFile));
        }

        return new static(require $configFile);
    }

    public function toArray()
    {
        return $this->configData;
    }

    public static function toFile($configFile, array $configData, $mode = 0600)
    {
        $fileData = sprintf('<?php return %s;', var_export($configData, true));
        FileIO::writeFile($configFile, $fileData, $mode);
    }
}
