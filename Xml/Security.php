<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */

// phpcs:disable Magento2.Functions.StaticFunction.StaticFunction

declare(strict_types=1);

namespace Wubinworks\CosmicStingPatch\Xml;

/**
 * XML security feature
 */
class Security extends \Laminas\Xml\Security
{
    /**
     * Verify if the input XML string contains ENTITY
     *
     * @param string $xml
     * @return bool
     */
    public static function hasEntity($xml): bool
    {
        try {
            self::heuristicScan($xml);
        } catch (\Laminas\Xml\Exception\RuntimeException $e) {
            return true;
        }

        return false;
    }
}
