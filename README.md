# Magento 2 patch for CVE-2024-34102(aka Cosmic Sting)

**Another way(as an extension) to fix CVE-2024-34102(XXE vulnerability) with extra XML Security enhancement. If you cannot upgrade Magento or cannot apply the official patch, this one is an alternative solution.**

**_If you don't fix this vulnerability, the attacker can RCE. We've already observed real world attacks._**

[![Magento 2 patch for CVE-2024-34102(aka Cosmic Sting)](https://raw.githubusercontent.com/wubinworks/home/master/images/Wubinworks/CosmicStingPatch/cosmic-sting-patch-v1.1.jpg "Magento 2 patch for CVE-2024-34102(aka Cosmic Sting)")](https://www.wubinworks.com/cosmic-sting-patch.html)

## CVE-2024-34102 Affected Magento Versions(starting from 2.3)

2.3.0 ~ 2.4.4-p8  
2.4.5 ~ 2.4.5-p7  
2.4.6 ~ 2.4.6-p5  
2.4.7

## Background

[CVE-2024-34102](https://cve.org/CVERecord?id=CVE-2024-34102)(aka Cosmic Sting) was identified as XXE vulnerability and the details were published on June 2024. By exploiting this vulnerability, the attacker can read secret and important configuration files on the server.  
Typically, the attacker will extract encryption keys in `env.php`.

In most hacked servers, we observed one or multiple of the followings:
 - Admin level WebAPI access with fake token
 - Fake orders
 - Unknown Admin accounts created
 - Backdoors
 - Magento core files modified
 - PHP script that steals sales data
 - Inject Javascript to CMS pages to steal credit cards
 - And maybe more

If you want to know _"How Exactly It Works"_, we have very detailed blog posts that [examine](https://www.wubinworks.com/blog/post/cve-2024-34102-cosmic-sting-attack) and [fix](https://www.wubinworks.com/blog/post/cve-2024-34102-aka-cosmicsting-how-to-defend) the vulnerability.

## Secondary Disasters(Very Important)

### Fake Admin Token

The attacker can craft fake Admin Token by using the stolen encryption key. With the fake Admin Token, the attacker is able to perform Admin level actions such as creating fake orders, modifying CMS Block to inject malicious Javascript and more.

### Chained with CVE-2024-2961

> XXEs are now RCEs

As CVE-2024-34102 enables the ability to read arbitrary file on the server, the attacker can now combine it with a bug([CVE-2024-2961](https://www.cve.org/CVERecord?id=CVE-2024-2961)) discovered in `glibc` to run any command on the server. One real case we experienced was that multiple backdoors got downloaded and installed.  
The `glibc` bug exists in `glibc` version <= 2.3.9

##### Check `glibc` version by running

```
ldd --version | grep -i 'libc'
```

## How to fix?

### Fix CVE-2024-34102

There are 3 Ways Available:

 - Upgrade Magento to an unaffected version(preferably the latest version)
 - Apply [official isolated patch](https://experienceleague.adobe.com/en/docs/commerce-knowledge-base/kb/troubleshooting/known-issues-patches-attached/security-update-available-for-adobe-commerce-apsb24-40-revised-to-include-isolated-patch-for-cve-2024-34102#isolated-patch-details)
 - Install this extension

**_Note you still need to fix "Secondary Disasters" after completing the above step._**

### Rotate Encryption Key

This step invalidates crafted fake tokens to completely deny WebAPI access from attacker.  
_If you are unsure whether encryption keys are leaked or not, do this step._

##### More Info

Some Magento 2.4 versions have a bug that you need to apply a [patch](https://github.com/wubinworks/magento2-jwt-auth-patch) before performing key rotation.

[How to rotate encryption key?](https://www.wubinworks.com/blog/post/magento2-rotate-encryption-key)

[Alternative Encryption Key Rotation Tool](https://github.com/wubinworks/magento2-encryption-key-manager-cli)

[New Magento encryption key format](https://www.wubinworks.com/blog/post/new-encryption-key-format-introduced-on-magento-2.4.7)

### Fix `glibc` Bug(Highly Recommended)

Update `glibc` to >= 2.40 to fix CVE-2024-2961.

## Requirements

Magento 2.3  
Magento 2.4

## Installation

**`composer require wubinworks/module-cosmic-sting-patch`**

_This extension requires dependencies that are not included in default Magento installation, so you need to use `composer`._

## ♥

If you like this extension or this extension helped you, please ★star☆ this repository.

You may also like:  
[Magento 2 patch for CVE-2022-24086, CVE-2022-24087](https://github.com/wubinworks/magento2-template-filter-patch)  
[Magento 2 Disable Customer Change Email Extension](https://github.com/wubinworks/disable-change-email)  
[Magento 2 Disable Customer Extension](https://github.com/wubinworks/magento2-disable-customer)
