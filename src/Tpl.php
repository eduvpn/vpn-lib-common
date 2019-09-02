<?php

/*
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2019, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace LC\Common;

use DateTime;
use DateTimeZone;
use LC\Common\Exception\TplException;
use RangeException;

class Tpl implements TplInterface
{
    /** @var array<string> */
    private $templateFolderList;

    /** @var array<string> */
    private $translationFileList;

    /** @var string|null */
    private $activeSectionName = null;

    /** @var array<string,string> */
    private $sectionList = [];

    /** @var array<string,array> */
    private $layoutList = [];

    /** @var array<string,mixed> */
    private $templateVariables = [];

    /** @var array<string,callable> */
    private $callbackList = [];

    /**
     * @param array<string> $templateFolderList
     * @param array<string> $translationFileList
     */
    public function __construct(array $templateFolderList, array $translationFileList = [])
    {
        $this->templateFolderList = $templateFolderList;
        $this->translationFileList = $translationFileList;
        $this->addCallback('bytes_to_human', [__CLASS__, 'toHuman']);
    }

    /**
     * @param array<string,mixed> $templateVariables
     *
     * @return void
     */
    public function addDefault(array $templateVariables)
    {
        $this->templateVariables = array_merge($this->templateVariables, $templateVariables);
    }

    /**
     * @param string   $callbackName
     * @param callable $cb
     *
     * @return void
     */
    public function addCallback($callbackName, callable $cb)
    {
        $this->callbackList[$callbackName] = $cb;
    }

    /**
     * @param string              $templateName
     * @param array<string,mixed> $templateVariables
     *
     * @return string
     */
    public function render($templateName, array $templateVariables = [])
    {
        $this->templateVariables = array_merge($this->templateVariables, $templateVariables);
        extract($this->templateVariables);
        ob_start();
        /** @psalm-suppress UnresolvableInclude */
        include $this->templatePath($templateName);
        $templateStr = ob_get_clean();
        if (0 === \count($this->layoutList)) {
            // we have no layout defined, so simple template...
            return $templateStr;
        }

        foreach ($this->layoutList as $templateName => $templateVariables) {
            unset($this->layoutList[$templateName]);
            $templateStr .= $this->render($templateName, $templateVariables);
        }

        return $templateStr;
    }

    /**
     * @param int $byteSize
     *
     * @return string
     */
    public static function toHuman($byteSize)
    {
        $kB = 1024;
        $MB = $kB * 1024;
        $GB = $MB * 1024;
        $TB = $GB * 1024;
        if ($byteSize > $TB) {
            return sprintf('%0.2f TiB', $byteSize / $TB);
        }
        if ($byteSize > $GB) {
            return sprintf('%0.2f GiB', $byteSize / $GB);
        }
        if ($byteSize > $MB) {
            return sprintf('%0.2f MiB', $byteSize / $MB);
        }

        return sprintf('%0.0f kiB', $byteSize / $kB);
    }

    /**
     * Trim a string to a specified lenght and escape it.
     *
     * @param string      $inputString
     * @param int         $maxLen
     * @param string|null $cb
     *
     * @throws \RangeException
     *
     * @return string
     */
    private function etr($inputString, $maxLen, $cb = null)
    {
        if ($maxLen < 3) {
            throw new RangeException('"maxLen" must be >= 3');
        }

        $strLen = mb_strlen($inputString);
        if ($strLen <= $maxLen) {
            return $inputString;
        }

        $partOne = mb_substr($inputString, 0, (int) ceil(($maxLen - 1) / 2));
        $partTwo = mb_substr($inputString, (int) -floor(($maxLen - 1) / 2));

        return $this->e($partOne.'…'.$partTwo, $cb);
    }

    /**
     * @param string              $templateName
     * @param array<string,mixed> $templateVariables
     *
     * @return string
     */
    private function insert($templateName, array $templateVariables = [])
    {
        return $this->render($templateName, $templateVariables);
    }

    /**
     * @param string $sectionName
     *
     * @return void
     */
    private function start($sectionName)
    {
        if (null !== $this->activeSectionName) {
            throw new TplException(sprintf('section "%s" already started', $this->activeSectionName));
        }

        $this->activeSectionName = $sectionName;
        ob_start();
    }

    /**
     * @param string $sectionName
     *
     * @return void
     */
    private function stop($sectionName)
    {
        if (null === $this->activeSectionName) {
            throw new TplException('no section started');
        }

        if ($sectionName !== $this->activeSectionName) {
            throw new TplException(
                sprintf(
                    'attempted to end section "%s" but current section is "%s"',
                    $sectionName,
                    $this->activeSectionName
                )
            );
        }

        $this->sectionList[$this->activeSectionName] = ob_get_clean();
        $this->activeSectionName = null;
    }

    /**
     * @param string              $layoutName
     * @param array<string,mixed> $templateVariables
     *
     * @return void
     */
    private function layout($layoutName, array $templateVariables = [])
    {
        $this->layoutList[$layoutName] = $templateVariables;
    }

    /**
     * @param string $sectionName
     *
     * @return string
     */
    private function section($sectionName)
    {
        if (!\array_key_exists($sectionName, $this->sectionList)) {
            throw new TplException(sprintf('section "%s" does not exist', $sectionName));
        }

        return $this->sectionList[$sectionName];
    }

    /**
     * @param string      $v
     * @param string|null $cb
     *
     * @return string
     */
    private function e($v, $cb = null)
    {
        if (null !== $cb) {
            $v = $this->batch($v, $cb);
        }

        return htmlentities($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * @param string $v
     * @param string $cb
     *
     * @return string
     */
    private function batch($v, $cb)
    {
        $functionList = explode('|', $cb);
        foreach ($functionList as $f) {
            if ('escape' === $f) {
                $v = $this->e($v);
                continue;
            }
            if (\array_key_exists($f, $this->callbackList)) {
                $f = $this->callbackList[$f];
            } else {
                if (!\function_exists($f)) {
                    throw new TplException(sprintf('function "%s" does not exist', $f));
                }
            }
            $v = \call_user_func($f, $v);
        }

        return $v;
    }

    /**
     * Format a date.
     *
     * @param string $dateString
     * @param string $dateFormat
     *
     * @return string
     */
    private function d($dateString, $dateFormat = 'Y-m-d H:i:s')
    {
        $dateTime = new DateTime($dateString);
        $dateTime->setTimeZone(new DateTimeZone(date_default_timezone_get()));

        return $this->e(date_format($dateTime, $dateFormat));
    }

    /**
     * @param string $v
     *
     * @return string
     */
    private function t($v)
    {
        // use original, unless it is found in any of the translation files...
        $translatedText = $v;
        foreach ($this->translationFileList as $translationFile) {
            // XXX should we make sure the file exists?
            /** @psalm-suppress UnresolvableInclude */
            $translationData = include $translationFile;
            if (\array_key_exists($v, $translationData)) {
                // translation found!
                $translatedText = $translationData[$v];
                // XXX do we want to loop over all of them?! take the first,
                // or the last?
                break;
            }
        }

        // find all string values, wrap the key, and escape the variable
        $escapedVars = [];
        foreach ($this->templateVariables as $k => $v) {
            if (\is_string($v)) {
                $escapedVars['%'.$k.'%'] = $this->e($v);
            }
        }

        return str_replace(array_keys($escapedVars), array_values($escapedVars), $translatedText);
    }

    /**
     * @param string $templateName
     *
     * @return bool
     */
    private function exists($templateName)
    {
        foreach ($this->templateFolderList as $templateFolder) {
            $templatePath = sprintf('%s/%s.php', $templateFolder, $templateName);
            if (file_exists($templatePath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $templateName
     *
     * @return string
     */
    private function templatePath($templateName)
    {
        foreach (array_reverse($this->templateFolderList) as $templateFolder) {
            $templatePath = sprintf('%s/%s.php', $templateFolder, $templateName);
            if (file_exists($templatePath)) {
                return $templatePath;
            }
        }

        throw new TplException(sprintf('template "%s" does not exist', $templateName));
    }
}
