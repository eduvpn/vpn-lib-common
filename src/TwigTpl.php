<?php

/**
 * eduVPN - End-user friendly VPN.
 *
 * Copyright: 2016-2017, The Commons Conservancy eduVPN Programme
 * SPDX-License-Identifier: AGPL-3.0+
 */

namespace SURFnet\VPN\Common;

use RuntimeException;
use Twig_Environment;
use Twig_Extensions_Extension_I18n;
use Twig_Loader_Filesystem;
use Twig_SimpleFilter;

class TwigTpl implements TplInterface
{
    /** @var string */
    private $localeDir;

    /** @var string */
    private $appName;

    /** @var Twig_Environment */
    private $twig;

    /** @var array */
    private $defaultVariables;

    /**
     * Create TwigTemplateManager.
     *
     * @param array  $templateDirs template directories to look in where later
     *                             paths override the earlier paths
     * @param string $localeDir
     * @param string $appName
     * @param string $cacheDir     the writable directory to store the cache
     */
    public function __construct(array $templateDirs, $localeDir, $appName, $cacheDir = null)
    {
        $existingTemplateDirs = [];
        foreach ($templateDirs as $templateDir) {
            if (false !== is_dir($templateDir)) {
                $existingTemplateDirs[] = $templateDir;
            }
        }
        $existingTemplateDirs = array_reverse($existingTemplateDirs);

        $environmentOptions = [
            'strict_variables' => true,
        ];

        if (null !== $cacheDir) {
            if (false === is_dir($cacheDir)) {
                if (false === @mkdir($cacheDir, 0700, true)) {
                    throw new RuntimeException('unable to create template cache directory');
                }
            }
            $environmentOptions['cache'] = $cacheDir;
        }
        $this->localeDir = $localeDir;
        $this->appName = $appName;
        $this->twig = new Twig_Environment(
            new Twig_Loader_Filesystem(
                $existingTemplateDirs
            ),
            $environmentOptions
        );

        $this->defaultVariables = [];
    }

    /**
     * @return void
     */
    public function setDefault(array $templateVariables)
    {
        $this->defaultVariables = $templateVariables;
    }

    /**
     * @return void
     */
    public function addDefault(array $templateVariables)
    {
        $this->defaultVariables = array_merge(
            $this->defaultVariables, $templateVariables
        );
    }

    /**
     * @param string $languageStr
     * @param string $localeDir
     *
     * @return void
     */
    public function setI18n($languageStr, $localeDir)
    {
        putenv(sprintf('LC_ALL=%s', $languageStr));

        if (false === setlocale(LC_ALL, [$languageStr, sprintf('%s.UTF-8', $languageStr)])) {
            throw new RuntimeException(sprintf('unable to set locale "%s"', $languageStr));
        }

        if ($localeDir !== bindtextdomain($this->appName, $localeDir)) {
            throw new RuntimeException('unable to bind text domain');
        }

        if (!is_string(bind_textdomain_codeset($this->appName, 'UTF-8'))) {
            throw new RuntimeException('unable to bind text domain codeset');
        }

        if ($this->appName !== textdomain($this->appName)) {
            throw new RuntimeException('unable to set text domain');
        }

        $this->twig->addExtension(new Twig_Extensions_Extension_I18n());
    }

    /**
     * @return void
     */
    public function addFilter(Twig_SimpleFilter $filter)
    {
        $this->twig->addFilter($filter);
    }

    /**
     * Render the template.
     *
     * @param string $templateName      the name of the template
     * @param array  $templateVariables the variables to be used in the
     *                                  template
     *
     * @return string the rendered template
     */
    public function render($templateName, array $templateVariables)
    {
        $uiLanguage = 'en_US';
        // determine default language
        if (array_key_exists('supportedLanguages', $this->defaultVariables)) {
            // take the first language of the supported languages as the default
            $uiLanguage = array_keys($this->defaultVariables['supportedLanguages'])[0];
        }

        if (array_key_exists('ui_lang', $_COOKIE)) {
            if (array_key_exists('supportedLanguages', $this->defaultVariables)) {
                if (array_key_exists($_COOKIE['ui_lang'], $this->defaultVariables['supportedLanguages'])) {
                    $uiLanguage = $_COOKIE['ui_lang'];
                }
            }
        }

        $this->setI18n($uiLanguage, $this->localeDir);
        $templateVariables = array_merge($this->defaultVariables, $templateVariables);

        return $this->twig->render(
            sprintf(
                '%s.twig',
                $templateName
            ),
            $templateVariables
        );
    }
}
