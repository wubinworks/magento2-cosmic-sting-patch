<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wubinworks\CosmicStingPatch\Model\Simplexml;

use Magento\Framework\App\ObjectManager;
use Wubinworks\XmlSecurity\Model\Xml\Security as XmlSecurity;

/**
 * A secure SimpleXMLElement that does not allow ENTITY
 * An alternative solution for CVE-2024-34102(aka Cosmic Sting)
 */
class Element extends \Magento\Framework\Simplexml\Element
{
    /**
     * Constructor
     *
     * @param string $data
     * @param int $options
     * @param bool $dataIsURL
     * @param string $namespaceOrPrefix
     * @param bool $isPrefix
     *
     * @throws \Wubinworks\CosmicStingPatch\Model\Exception\InvalidArgumentException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        string $data,
        int $options = 0,
        bool $dataIsURL = false,
        string $namespaceOrPrefix = "",
        bool $isPrefix = false
    ) {
        /** @var \Magento\Framework\Xml\Security $xmlSecurity */
        $xmlSecurity = ObjectManager::getInstance()->get(XmlSecurity::class);
        if (!$xmlSecurity->scan($data)) {
            throw new \Wubinworks\CosmicStingPatch\Model\Exception\InvalidArgumentException(
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
