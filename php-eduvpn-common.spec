%global composer_vendor         eduvpn
%global composer_project        common
%global composer_namespace      SURFnet/VPN/Common

%global github_owner            eduvpn
%global github_name             vpn-lib-common
%global github_commit           632bc417575a923622c1ffd23008864ea39a4680
%global github_short            %(c=%{github_commit}; echo ${c:0:7})
%if 0%{?rhel} == 5
%global with_tests              0%{?_with_tests:1}
%else
%global with_tests              0%{!?_without_tests:1}
%endif

Name:       php-%{composer_vendor}-%{composer_project}
Version:    1.0.0
Release:    0.20%{?dist}
Summary:    Common VPN library

Group:      System Environment/Libraries
License:    AGPLv3+

URL:        https://github.com/%{github_owner}/%{github_name}
Source0:    %{url}/archive/%{github_commit}/%{name}-%{version}-%{github_short}.tar.gz

BuildArch:  noarch
BuildRoot:  %{_tmppath}/%{name}-%{version}-%{release}-root-%(%{__id_u} -n) 

%if %{with_tests}
BuildRequires:  php(language) >= 5.4.0
BuildRequires:  php-filter
BuildRequires:  php-hash
BuildRequires:  php-json
BuildRequires:  php-mbstring
BuildRequires:  php-session
BuildRequires:  php-spl
BuildRequires:  php-composer(guzzlehttp/guzzle) >= 5.3.0
BuildRequires:  php-composer(guzzlehttp/guzzle) < 6.0.0
BuildRequires:  php-composer(psr/log)
BuildRequires:  php-composer(symfony/polyfill)
BuildRequires:  php-composer(symfony/yaml)
BuildRequires:  php-composer(symfony/class-loader)
BuildRequires:  %{_bindir}/phpunit
BuildRequires:  %{_bindir}/phpab
%endif

Requires:   php(language) >= 5.4.0
Requires:   php-filter
Requires:   php-hash
Requires:   php-json
Requires:   php-mbstring
Requires:   php-session
Requires:   php-spl
Requires:   php-composer(guzzlehttp/guzzle) >= 5.3.0
Requires:   php-composer(guzzlehttp/guzzle) < 6.0.0
Requires:   php-composer(psr/log)
Requires:   php-composer(symfony/polyfill)
Requires:   php-composer(symfony/yaml)
Requires:   php-composer(symfony/class-loader)

Provides:   php-composer(%{composer_vendor}/%{composer_project}) = %{version}

%description
Common VPN library.

%prep
%setup -qn %{github_name}-%{github_commit}

%build
: Create autoloader
cat <<'AUTOLOAD' | tee src/%{composer_namespace}/autoload.php
<?php
/**
 * Autoloader for %{name} and its' dependencies
 * (created by %{name}-%{version}-%{release}).
 *
 * @return \Symfony\Component\ClassLoader\ClassLoader
 */

if (!isset($fedoraClassLoader) || !($fedoraClassLoader instanceof \Symfony\Component\ClassLoader\ClassLoader)) {
    if (!class_exists('Symfony\\Component\\ClassLoader\\ClassLoader', false)) {
        require_once '%{phpdir}/Symfony/Component/ClassLoader/ClassLoader.php';
    }

    $fedoraClassLoader = new \Symfony\Component\ClassLoader\ClassLoader();
    $fedoraClassLoader->register();
}

$fedoraClassLoader->addPrefix('SURFnet\\VPN\\Common\\', dirname(dirname(dirname(__DIR__))));

// Required dependency
require_once '%{phpdir}/GuzzleHttp/autoload.php';
require_once '%{phpdir}/Psr/Log/autoload.php';
require_once '%{phpdir}/Symfony/Polyfill/autoload.php';
require_once '%{phpdir}/Symfony/Component/Yaml/autoload.php';

return $fedoraClassLoader;
AUTOLOAD


%install
rm -rf %{buildroot} 
mkdir -p ${RPM_BUILD_ROOT}%{_datadir}/php
cp -pr src/* ${RPM_BUILD_ROOT}%{_datadir}/php

%if %{with_tests} 
%check
%{_bindir}/phpab --output tests/bootstrap.php tests
echo 'require "%{buildroot}%{_datadir}/php/%{composer_namespace}/autoload.php";' >> tests/bootstrap.php
%{_bindir}/phpunit \
    --bootstrap tests/bootstrap.php
%endif

%clean
rm -rf %{buildroot}

%files
%defattr(-,root,root,-)
%{_datadir}/php/%{composer_namespace}
%doc README.md composer.json
%{!?_licensedir:%global license %%doc} 
%license LICENSE

%changelog
* Sun Oct 09 2016 François Kooman <fkooman@tuxed.net> - 1.0.0-0.20
- update to 632bc417575a923622c1ffd23008864ea39a4680
