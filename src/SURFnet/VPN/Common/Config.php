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
    private $configData;

    public function __construct(array $configData)
    {
        $this->configData = array_merge(self::defaultConfig(), $configData);
    }

    public static function defaultConfig()
    {
        return [];
    }

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

    public static function fromFile($configFile)
    {
        if (false === $fileContent = @file_get_contents($configFile)) {
            throw new RuntimeException(sprintf('unable to read configuration file "%s"', $configFile));
        }

        $parsedConfig = Yaml::parse($fileContent);

        if (!is_array($parsedConfig)) {
            throw new RuntimeException(sprintf('invalid configuration file format in "%s"', $configFile));
        }

        return new static($parsedConfig);
    }
}
