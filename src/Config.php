<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common;

use LC\Common\Exception\ConfigException;

class Config
{
    /** @var array */
    protected $configData;

    public function __construct(array $configData)
    {
        $this->configData = $configData;
        $this->configData = array_merge(static::defaultConfig(), $configData);
    }

    /**
     * @return array
     */
    public static function defaultConfig()
    {
        return [];
    }

    /**
     * @param string $k
     *
     * @return self
     */
    public function s($k)
    {
        if (!\array_key_exists($k, $this->configData)) {
            return new self([]);
        }
        if (!\is_array($this->configData[$k])) {
            throw new ConfigException('key "'.$k.'" not of type array');
        }

        return new self($this->configData[$k]);
    }

    /**
     * @param string $k
     *
     * @return string|null
     */
    public function optionalString($k)
    {
        if (!\array_key_exists($k, $this->configData)) {
            return null;
        }
        if (!\is_string($this->configData[$k])) {
            throw new ConfigException('key "'.$k.'" not of type string');
        }

        return $this->configData[$k];
    }

    /**
     * @param string      $k
     * @param string|null $d
     *
     * @return string
     */
    public function requireString($k, $d = null)
    {
        if (null === $v = $this->optionalString($k)) {
            if (null !== $d) {
                return $d;
            }

            throw new ConfigException('key "'.$k.'" not available');
        }

        return $v;
    }

    /**
     * @param string $k
     *
     * @return int|null
     */
    public function optionalInt($k)
    {
        if (!\array_key_exists($k, $this->configData)) {
            return null;
        }
        if (!\is_int($this->configData[$k])) {
            throw new ConfigException('key "'.$k.'" not of type int');
        }

        return $this->configData[$k];
    }

    /**
     * @param string   $k
     * @param int|null $d
     *
     * @return int
     */
    public function requireInt($k, $d = null)
    {
        if (null === $v = $this->optionalInt($k)) {
            if (null !== $d) {
                return $d;
            }

            throw new ConfigException('key "'.$k.'" not available');
        }

        return $v;
    }

    /**
     * @param string $k
     *
     * @return bool|null
     */
    public function optionalBool($k)
    {
        if (!\array_key_exists($k, $this->configData)) {
            return null;
        }
        if (!\is_bool($this->configData[$k])) {
            throw new ConfigException('key "'.$k.'" not of type bool');
        }

        return $this->configData[$k];
    }

    /**
     * @param string    $k
     * @param bool|null $d
     *
     * @return bool
     */
    public function requireBool($k, $d = null)
    {
        if (null === $v = $this->optionalBool($k)) {
            if (null !== $d) {
                return $d;
            }

            throw new ConfigException('key "'.$k.'" not available');
        }

        return $v;
    }

    /**
     * @param string $k
     *
     * @return array|null
     */
    public function optionalArray($k)
    {
        if (!\array_key_exists($k, $this->configData)) {
            return null;
        }
        if (!\is_array($this->configData[$k])) {
            throw new ConfigException('key "'.$k.'" not of type array');
        }

        return $this->configData[$k];
    }

    /**
     * @param string $k
     *
     * @return array
     */
    public function requireArray($k, array $d = null)
    {
        if (null === $v = $this->optionalArray($k)) {
            if (null !== $d) {
                return $d;
            }

            throw new ConfigException('key "'.$k.'" not available');
        }

        return $v;
    }

    /**
     * @deprecated
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return self
     */
    public function setItem($key, $value)
    {
        $this->configData[$key] = $value;

        return $this;
    }

    /**
     * @deprecated
     *
     * @param string     $key
     * @param mixed|null $defaultValue
     *
     * @return mixed
     */
    public function optionalItem($key, $defaultValue = null)
    {
        if (!\array_key_exists($key, $this->configData)) {
            return $defaultValue;
        }

        return $this->configData[$key];
    }

    /**
     * @psalm-suppress UnresolvableInclude
     *
     * @param string $configFile
     *
     * @return Config
     */
    public static function fromFile($configFile)
    {
        if (false === FileIO::exists($configFile)) {
            throw new ConfigException(sprintf('unable to read "%s"', $configFile));
        }

        return new self(require $configFile);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->configData;
    }

    /**
     * @param string $configFile
     * @param int    $mode
     *
     * @return void
     */
    public static function toFile($configFile, array $configData, $mode = 0600)
    {
        $fileData = sprintf('<?php return %s;', var_export($configData, true));
        FileIO::writeFile($configFile, $fileData, $mode);
    }
}
