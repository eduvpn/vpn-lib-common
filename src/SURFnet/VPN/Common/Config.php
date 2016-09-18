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

use Symfony\Component\Yaml\Yaml;
use SURFnet\VPN\Common\Exception\ConfigException;
use InvalidArgumentException;
use RuntimeException;

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

    /**
     * Get a configuration value.
     */
    public function v()
    {
        $argv = func_get_args();
        // if no parameters are requested, return everything
        if (0 === count($argv)) {
            return $this->configData;
        }

        $configPointer = $this->configData;
        foreach ($argv as $arg) {
            if (!is_string($arg)) {
                throw new InvalidArgumentException('requested configuration field must be string');
            }
            if (!is_array($configPointer) || !array_key_exists($arg, $configPointer)) {
                throw new ConfigException(sprintf('missing configuration field "%s"', implode(',', $argv)));
            }
            $configPointer = $configPointer[$arg];
        }

        return $configPointer;
    }

    /**
     * Check if a configuration value exists.
     */
    public function e()
    {
        $argv = func_get_args();
        if (0 === count($argv)) {
            return true;
        }

        $configPointer = $this->configData;
        foreach ($argv as $arg) {
            if (!is_string($arg)) {
                throw new InvalidArgumentException('requested configuration field must be string');
            }
            if (!is_array($configPointer) || !array_key_exists($arg, $configPointer)) {
                return false;
            }
            $configPointer = $configPointer[$arg];
        }

        return true;
    }

    public static function fromFile($configFile)
    {
        $fileContent = FileIO::readFile($configFile);
        $parsedConfig = Yaml::parse($fileContent);
        if (!is_array($parsedConfig)) {
            throw new RuntimeException(sprintf('invalid configuration file format in "%s"', $configFile));
        }

        return new static($parsedConfig);
    }

    public static function toFile($configFile, array $configData)
    {
        $yamlData = Yaml::dump($configData);
        FileIO::writeFile($configFile, $yamlData);
    }
}
