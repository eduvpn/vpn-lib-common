<?php

/**
 * Autoloader for eduvpn/common.
 */
$vendorDir = '/usr/share/php';

// Use Symfony autoloader
if (!isset($fedoraClassLoader) || !($fedoraClassLoader instanceof \Symfony\Component\ClassLoader\ClassLoader)) {
    if (!class_exists('Symfony\\Component\\ClassLoader\\ClassLoader', false)) {
        require_once $vendorDir.'/Symfony/Component/ClassLoader/ClassLoader.php';
    }

    $fedoraClassLoader = new \Symfony\Component\ClassLoader\ClassLoader();
    $fedoraClassLoader->register();
}
$fedoraClassLoader->addPrefixes(array(
    'SURFnet\\VPN\\Common' => dirname(dirname(dirname(__DIR__))),
));

require_once $vendorDir.'/GuzzleHttp/autoload.php';
require_once $vendorDir.'/Psr/Log/autoload.php';
require_once $vendorDir.'/Symfony/Polyfill/autoload.php';
require_once $vendorDir.'/Symfony/Component/Yaml/autoload.php';
