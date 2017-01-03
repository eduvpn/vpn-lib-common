<?php
/**
 *  Copyright (C) 2016 SURFnet.
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as
 *  published by the Free Software Foundation, either version 3 of the
 *  License, or (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace SURFnet\VPN\Common;

use SURFnet\VPN\Common\Exception\ConfigException;

class Config
{
    /** @var array */
    protected $configData;

    public function __construct(array $configData)
    {
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

        return new static($this->configData[$key]);
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
