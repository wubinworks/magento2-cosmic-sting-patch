<?php
/**
 * Copyright © Wubinworks. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Wubinworks\CosmicStingPatch\Model;

use Laminas\Http\PhpEnvironment\RemoteAddress as LaminasHttpRemoteAddress;
use Zend\Http\PhpEnvironment\RemoteAddress as ZendHttpRemoteAddress;
use Magento\Framework\HTTP\PhpEnvironment\Request;

/**
 * Request information
 */
class RequestInfo
{
    public const INFO_KEYS = [
        'ip',
        'request_line',
        'body'
    ];

    /**
     * @var Request
     */
    protected $request;

    /**
     * Constructor
     *
     * @param Request $request
     */
    public function __construct(
        Request $request
    ) {
        $this->request = $request;
    }

    /**
     * Get request information
     *
     * @param ?string $key
     * @return string|string[]
     *
     * @throws \InvalidArgumentException
     */
    public function getRequestInfo(?string $key = null)
    {
        $info = [
            'ip' => (string)$this->getRemoteAddress(), // `false` converted to empty string
            'request_line' => $this->getRequestLine(),
            'body' => $this->getRequestBody()
        ];
        if ($key === null) {
            return $info;
        }
        if (!array_key_exists($key, $info)) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown key %s was used to retrieve RequestInfo.',
                $key
            ));
        }
        return $info[$key];
    }

    /**
     * Get correct remote IP address
     *
     * @return string|bool `false` if failed
     */
    protected function getRemoteAddress()
    {
        if (class_exists(LaminasHttpRemoteAddress::class)) {
            $httpRemoteAddressClass = LaminasHttpRemoteAddress::class;
        } else {
            $httpRemoteAddressClass = ZendHttpRemoteAddress::class;
        }

        $ip = (new $httpRemoteAddressClass())->getIpAddress();
        if ($ip) {
            return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6);
        }

        return false;
    }

    /**
     * Get request line
     *
     * @return string
     */
    protected function getRequestLine(): string
    {
        return (string)$this->request->renderRequestLine();
    }

    /**
     * Get request body with limited length
     *
     * @param int $maxLength
     * @return string
     */
    protected function getRequestBody(int $maxLength = 1000): string
    {
        return mb_substr((string)$this->request->getContent(), 0, $maxLength);
    }
}
