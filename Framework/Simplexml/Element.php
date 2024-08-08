<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wubinworks\CosmicStingPatch\Framework\Simplexml;

use Wubinworks\CosmicStingPatch\Xml\Security as XmlSecurity;

/**
 * A safer SimpleXMLElement
 * Patch for CVE-2024-34102(aka CosmicSting)
 *
 * @see https://nvd.nist.gov/vuln/detail/CVE-2024-34102
 * @see https://helpx.adobe.com/security/products/magento/apsb24-40.html
 * @see https://experienceleague.adobe.com/en/docs/commerce-knowledge-base/kb/troubleshooting/known-issues-patches-attached/security-update-available-for-adobe-commerce-apsb24-40-revised-to-include-isolated-patch-for-cve-2024-34102
 */
class Element extends \Magento\Framework\Simplexml\Element
{
    /**
     * PHP SimpleXMLElement constructor
     *
     * @param string $data
     * @param int $options
     * @param bool $dataIsURL
     * @param string $namespaceOrPrefix
     * @param bool $isPrefix
     *
     * @throws \Laminas\Xml\Exception\InvalidArgumentException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        string $data,
        int $options = 0,
        bool $dataIsURL = false,
        string $namespaceOrPrefix = "",
        bool $isPrefix = false
    ) {
        if (XmlSecurity::hasEntity($data)) {
            throw new \Laminas\Xml\Exception\InvalidArgumentException(
                'Input XML string should not contain ENTITY.'
            );
        }
        parent::__construct(
            $data,
            $options,
            false,
            $namespaceOrPrefix,
            $isPrefix
        );
    }
}
