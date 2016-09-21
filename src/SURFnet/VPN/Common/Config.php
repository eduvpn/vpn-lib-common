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
     * Get value (do not check type).
     */
    public function v()
    {
        return $this->getValue(func_get_args());
    }

    /**
     * Get string.
     */
    public function s()
    {
        return $this->getValue(func_get_args(), 'is_string');
    }

    /**
     * Get integer.
     */
    public function i()
    {
        return $this->getValue(func_get_args(), 'is_int');
    }

    /**
     * Get boolean.
     */
    public function b()
    {
        return $this->getValue(func_get_args(), 'is_bool');
    }

    /**
     * Get array.
     */
    public function a()
    {
        return $this->getValue(func_get_args(), 'is_array');
    }

    /**
     * Check if a configuration value exists.
     */
    public function e()
    {
        return $this->getValue(func_get_args(), null, true);
    }

    /**
     * Get a configuration value.
     */
    private function getValue(array $argv, $typeValidator = null, $checkOnly = false)
    {
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
                if ($checkOnly) {
                    return false;
                }

                throw new ConfigException(sprintf('missing configuration field "%s"', implode(',', $argv)));
            }
            $configPointer = $configPointer[$arg];
        }

        // check if the value is of the expected type, if we only check if it
        // exists, the typeValidator is 'null' so we do not check the type
        if (!is_null($typeValidator) && !$typeValidator($configPointer)) {
            throw new ConfigException(sprintf('the value of configuration field "%s" does not pass validator "%s"', implode(',', $argv), $typeValidator));
        }

        if ($checkOnly) {
            return true;
        }

        return $configPointer;
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
