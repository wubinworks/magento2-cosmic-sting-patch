# Magento 2 Patch for CVE-2024-34102(aka Cosmic Sting)

**An alternative solution(as a Magento 2 extension) to fix the XXE vulnerability CVE-2024-34102(aka Cosmic Sting). If you cannot upgrade Magento or cannot apply the official patch, try this one.**

**_If you don't fix this vulnerability, the attacker can RCE. We've already observed real world attacks._**

[![Magento 2 CVE-2024-34102(aka Cosmic Sting) Patch](https://raw.githubusercontent.com/wubinworks/home/master/images/Wubinworks/CosmicStingPatch/cosmic-sting-patch-v1.1.jpg "Magento 2 CVE-2024-34102(aka Cosmic Sting) Patch")](https://www.wubinworks.com/cosmic-sting-patch.html)

## CVE-2024-34102 Affected Magento Versions(starting from 2.3)

2.3.0 ~ 2.4.4-p8  
2.4.5 ~ 2.4.5-p7  
2.4.6 ~ 2.4.6-p5  
2.4.7

## Background

[CVE-2024-34102](https://cve.org/CVERecord?id=CVE-2024-34102)(aka Cosmic Sting) was identified as [XXE](https://en.wikipedia.org/wiki/XML_external_entity_attack) vulnerability and the details were published on June 2024. By exploiting this vulnerability, the attacker can read secret and important configuration files on the server.  
Typically, the attacker will extract encryption keys in `env.php`.

In most hacked servers, we observed one or multiple of the followings:
 - Admin level WebAPI access with fake token
 - Fake orders
 - Unknown Admin accounts created
 - Backdoors
 - Magento core files modified
 - PHP script that steals sales data
 - Inject malicious Javascript to CMS pages to steal credit cards
 - And maybe more

If you want to know _"How Exactly It Works"_, we have very detailed blog posts that [examine](https://www.wubinworks.com/blog/post/cve-2024-34102-cosmic-sting-attack) and [fix](https://www.wubinworks.com/blog/post/cve-2024-34102-aka-cosmicsting-how-to-defend) the vulnerability.

## Secondary Disasters(Very Important)

### Fake Admin Token

The attacker can craft fake Admin Token by using the stolen encryption key. With the fake Admin Token, the attacker is able to perform Admin level actions such as creating fake orders, modifying CMS Block to inject malicious Javascript and more.

### Chained with CVE-2024-2961

> XXEs are now RCEs.

As CVE-2024-34102 enables the ability to read arbitrary file on the server, the attacker can now combine it with a bug([CVE-2024-2961](https://www.cve.org/CVERecord?id=CVE-2024-2961)) discovered in `glibc` to run any command on the server. One real case we experienced was that multiple backdoors got downloaded and installed.  
The `glibc` bug exists in `glibc` version <= 2.3.9

##### Check `glibc` version by running

```bash
ldd --version | grep -i 'libc'
```

## How to fix?

### Fix the Main Vulnerability CVE-2024-34102

There are 3 Ways Available:

 - Upgrade Magento to an unaffected version(preferably the latest version)
 - Apply [official isolated patch](https://experienceleague.adobe.com/en/docs/commerce-knowledge-base/kb/troubleshooting/known-issues-patches-attached/security-update-available-for-adobe-commerce-apsb24-40-revised-to-include-isolated-patch-for-cve-2024-34102#isolated-patch-details)
 - Install this extension

**_Note you still need to fix "Secondary Disasters" after completing the above step._**

### Rotate Encryption Key

This step invalidates crafted fake tokens to completely deny WebAPI access from attacker.  
_If you are unsure whether encryption keys are leaked or not, do this step._

##### Additional Info

Some Magento 2.4 versions have a bug that you need to apply a [patch](https://github.com/wubinworks/magento2-jwt-auth-patch) before performing key rotation.

[How to rotate encryption key?](https://www.wubinworks.com/blog/post/magento2-rotate-encryption-key)

[Alternative Encryption Key Rotation Tool](https://github.com/wubinworks/magento2-encryption-key-manager-cli)

[New Magento encryption key format](https://www.wubinworks.com/blog/post/new-encryption-key-format-introduced-on-magento-2.4.7)

### Fix `glibc` Bug(Strongly Recommended)

Update `glibc` to >= 2.40 to fix CVE-2024-2961.

_Don't forget to reboot server._

## Feature

This extension

 - Fixes CVE-2024-34102(Can PASS the [Official Security Scan Tool](https://account.magento.com/scanner/dashboard/))
 - Version 1.2.0 new feature: _Who Attacked My Site_  
For those who are interested in the attacker, check [Logging](#logging) section.

## Logging

##### Enable Logging

By default, logging is disabled for performance consideration. To enable, open a local module and merge the following to `etc/di.xml`.

```xml
<?xml version="1.0"?>
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
    <type name="Wubinworks\CosmicStingPatch\Plugin\Framework\Webapi\ServiceInputProcessor">
        <arguments>
            <argument name="loggerConfig" xsi:type="array">
                <item name="ip" xsi:type="array">
                    <item name="enabled" xsi:type="boolean">true</item>
                </item>
            </argument>
        </arguments>
    </type>
</config>
```

##### Log Location

```text
<magento_root>/var/log/wubinworks_cve-2024-34102.log
```

##### Incorrect IP Address?

If you got incorrect IP such as `127.0.0.1`, empty string or a CDN's IP, this means your ***web server, middleware, and/or proxy server have incorrect settings***. There is no way to tell the real IP address without fixing those incorrect settings.

## Requirements

Magento 2.3 or 2.4

##### PHP Version Compatibility

Version 1.0.0 and 1.1.0 support PHP 8 only  
Version 1.2.0(re-designed) supports PHP 7 and PHP 8

## Installation

Latest:  
**`composer require wubinworks/module-cosmic-sting-patch`**

Installation Tips:  
 - _Version 1.0.0 and 1.1.0 must be installed via `composer`_
 - _Version 1.2.0 can be installed via `composer` or directly to `app/code`_

## ♥

If you like this extension or this extension helped you, please ★star☆ this repository.

You may also like:  
[Magento 2 Patch for CVE-2022-24086, CVE-2022-24087](https://github.com/wubinworks/magento2-template-filter-patch)  
[Magento 2 Enhanced XML Security](https://github.com/wubinworks/magento2-enhanced-xml-security)  
[Magento 2 Encryption Key Manager CLI](https://github.com/wubinworks/magento2-encryption-key-manager-cli)  
[Magento 2 JWT Authentication Patch](https://github.com/wubinworks/magento2-jwt-auth-patch)  

[Magento 2 Disable Customer Change Email Extension](https://github.com/wubinworks/disable-change-email)  
[Magento 2 Disable Customer Extension](https://github.com/wubinworks/magento2-disable-customer)
