%global composer_vendor         eduvpn
%global composer_project        common
%global composer_namespace      SURFnet/VPN/Common

%global github_owner            eduvpn
%global github_name             vpn-lib-common
%global github_commit           1db8bce1a3bf656277b8b70e37ba6b0ff8ccf618
%global github_short            %(c=%{github_commit}; echo ${c:0:7})
%if 0%{?rhel} == 5
%global with_tests              0%{?_with_tests:1}
%else
%global with_tests              0%{!?_without_tests:1}
%endif

Name:       php-%{composer_vendor}-%{composer_project}
Version:    1.0.0
Release:    0.19%{?dist}
Summary:    Common VPN library

Group:      System Environment/Libraries
License:    AGPLv3+

URL:        https://github.com/%{github_owner}/%{github_name}
Source0:    %{url}/archive/%{github_commit}/%{name}-%{version}-%{github_short}.tar.gz
Source1:    %{name}-autoload.php

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
cp %{SOURCE1} src/%{composer_namespace}/autoload.php

%build

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
* Fri Oct 07 2016 François <fkooman@tuxed.net> - 1.0.0-0.19
- rebuilt

* Fri Oct 07 2016 François <fkooman@tuxed.net> - 1.0.0-0.18
- rebuilt

* Fri Oct 07 2016 François Kooman <fkooman@tuxed.net> - 1.0.0-0.17
- update to 65466591a3fc3c5a2e519388725145cb6d7bd9d2

* Thu Oct 06 2016 François Kooman <fkooman@tuxed.net> - 1.0.0-0.16
- update to 622a44de85f8051ca1cf9d94acfe657891bd13b5

* Thu Oct 06 2016 François Kooman <fkooman@tuxed.net> - 1.0.0-0.15
- update to 36ab70a53d52da51bcd687a57750d188f454759c

* Wed Oct 05 2016 François Kooman <fkooman@tuxed.net> - 1.0.0-0.14
- update to 5e0c08f9915c8f9c864f8adc3a2d8270e2ad0f3b

* Tue Oct 04 2016 François Kooman <fkooman@tuxed.net> - 1.0.0-0.13
- update to 59b0880b8c14ce73b03bbd78d3c56a88bcfba429

* Thu Sep 29 2016 François Kooman <fkooman@tuxed.net> - 1.0.0-0.12
- update to ba440885cfa42021ee5b87fb9fe7b992909866b1

* Mon Sep 26 2016 François Kooman <fkooman@tuxed.net> - 1.0.0-0.11
- update to f587768996cf921e9bf10e982a8169f37a46a25e

* Fri Sep 23 2016 François Kooman <fkooman@tuxed.net> - 1.0.0-0.10
- update to a866037c195822a215770d48850247c67a96b882

* Wed Sep 21 2016 François Kooman <fkooman@tuxed.net> - 1.0.0-0.9
- update to b4fb0626c6c6ba58d6367a7845c121916535fd1e

* Tue Sep 20 2016 François Kooman <fkooman@tuxed.net> - 1.0.0-0.8
- update to 497e2d63edefdc631ad86dc89f0a30176d45032d

* Mon Sep 19 2016 François Kooman <fkooman@tuxed.net> - 1.0.0-0.7
- update to e1236372a87fca49cc0f05585c0f0fe8a0a3b413

* Sun Sep 18 2016 François Kooman <fkooman@tuxed.net> - 1.0.0-0.6
- fix Guzzle dependency version

* Sun Sep 18 2016 François Kooman <fkooman@tuxed.net> - 1.0.0-0.5
- rebuilt

* Sun Sep 18 2016 François Kooman <fkooman@tuxed.net> - 1.0.0-0.4
- update to b50d161f7caa25c67a17b7fa3a6671447cac08e9

* Thu Sep 15 2016 François Kooman <fkooman@tuxed.net> - 1.0.0-0.3
- update to d1a3e49b5d9653497bd7f479409724fdb3377f81

* Thu Sep 15 2016 François Kooman <fkooman@tuxed.net> - 1.0.0-0.2
- update to 7ff266d86053d14dad01f32b6e41d17ef59d06a4

* Wed Sep 14 2016 François Kooman <fkooman@tuxed.net> - 1.0.0-0.1
- initial package
