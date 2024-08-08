# Magento 2 patch for CVE-2024-34102(aka CosmicSting)
**Another way(as an extension) to hotfix the security hole if you cannot apply the official patch or cannot upgrade Magento.**

# Description

### Impact

The attacker makes use of this security hole may read secret files(eg: encryption key in `env.php`) on the server.\
With those secrets, the attacker can perform unauthorized actions(eg: by creating admin JSON Web Token `JWT`).

### More Info

[CVE-2024-34102](https://nvd.nist.gov/vuln/detail/CVE-2024-34102)\
[Official Patch](https://helpx.adobe.com/security/products/magento/apsb24-40.html)

# Requirements
**Magento 2.4**

# Installation
Note: it has a dependency, so you need `composer`.\
**`composer require wubinworks/module-cosmic-sting-patch`**
