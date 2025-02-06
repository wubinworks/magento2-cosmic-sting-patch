<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wubinworks\CosmicStingPatch\Plugin\Framework\Webapi;

use Magento\Framework\Phrase;
use Magento\Framework\Exception\SerializationException;
use Wubinworks\CosmicStingPatch\Model\RequestInfo;

/**
 * Patch for CVE-2024-34102(aka Cosmic Sting)
 *
 * @link https://nvd.nist.gov/vuln/detail/CVE-2024-34102
 * @link https://helpx.adobe.com/security/products/magento/apsb24-40.html
 * @link https://experienceleague.adobe.com/en/docs/commerce-knowledge-base/kb/troubleshooting/known-issues-patches-attached/security-update-available-for-adobe-commerce-apsb24-40-revised-to-include-isolated-patch-for-cve-2024-34102
 */
class ServiceInputProcessor
{
    /**
     * Including inherited classes
     *
     * @var string[]
     */
    protected $forbiddenClasses = [
        \SimpleXMLElement::class,
        \DOMElement::class
    ];

    /**
     * @var RequestInfo
     */
    protected $requestInfo;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var array
     */
    protected $loggerConfig;

    /**
     * Constructor
     *
     * @param RequestInfo $requestInfo
     * @param \Psr\Log\LoggerInterface $logger
     * @param array $loggerConfig
     */
    public function __construct(
        RequestInfo $requestInfo,
        \Psr\Log\LoggerInterface $logger,
        array $loggerConfig = []
    ) {
        $this->requestInfo = $requestInfo;
        $this->logger = $logger;
        $this->loggerConfig = array_merge($this->_initLoggerConfig(), $loggerConfig);
    }

    /**
     * Before plugin to detect forbidden type
     *
     * @param \Magento\Framework\Webapi\ServiceInputProcessor $subject
     * @param mixed $data
     * @param string $type
     * @return null
     *
     * @throws SerializationException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeConvertValue(
        \Magento\Framework\Webapi\ServiceInputProcessor $subject,
        $data,
        $type
    ) {
        $type = (string)$type;
        if ($this->isForbiddenType($type)) {
            $message = $this->prepareLogMessage();
            if ($message) {
                $this->logger->info($message);
            }
            throw new SerializationException(
                new Phrase('Invalid data type detected in deserialization process.')
            );
        }

        return null;
    }

    /**
     * Check forbidden type
     *
     * @param string $type
     * @return bool
     */
    protected function isForbiddenType(string $type): bool
    {
        foreach ($this->forbiddenClasses as $forbiddenClass) {
            if (is_subclass_of($type, $forbiddenClass)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Prepare log message
     *
     * @return string
     */
    protected function prepareLogMessage(): string
    {
        if (!$this->isLoggerEnabled()) {
            return '';
        }

        $message = 'Detected possible Cosmic Sting attack.' . "\n";
        if ($this->loggerConfig['ip']['enabled']) {
            $message .= 'IP: ' . $this->requestInfo->getRequestInfo('ip') . "\n";
        }
        if ($this->loggerConfig['request_line']['enabled']) {
            $message .= $this->requestInfo->getRequestInfo('request_line') . "\n";
        }
        if ($this->loggerConfig['body']['enabled']) {
            $message .= $this->requestInfo->getRequestInfo('body') . "\n";
        }

        return $message;
    }

    /**
     * Initialize logger config
     *
     * @return array
     */
    protected function _initLoggerConfig(): array
    {
        $result = [];
        foreach (RequestInfo::INFO_KEYS as $key) {
            $result[$key]['enabled'] = false;
        }
        return $result;
    }

    /**
     * Check logger enabled
     *
     * @return bool
     */
    protected function isLoggerEnabled(): bool
    {
        foreach ($this->loggerConfig as $item) {
            if ($item['enabled']) {
                return true;
            }
        }
        return false;
    }
}
