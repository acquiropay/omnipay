<?php

namespace Omnipay\AcquiroPay\Message;

use Omnipay\Common\Exception\RuntimeException;
use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;
use Omnipay\Common\Message\ResponseInterface;

/**
 * AcquiroPay Abstract Request
 *
 * This is the parent class for all AcquiroPay requests.
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
    /**
     * Live Endpoint URL.
     *
     * @var string URL
     */
    protected $liveEndpoint = 'https://gateway.acquiropay.com';

    /**
     * Test Endpoint URL.
     *
     * @var string URL
     */
    protected $testEndpoint = 'https://gateway.acqp.co';

    /**
     * Get a merchant id.
     *
     * @return string|null
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    /**
     * Set a merchant id.
     *
     * @param string $value
     * @return BaseAbstractRequest|AbstractRequest
     * @throws RuntimeException
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    /**
     * Get a merchant's product id.
     *
     * @return string|null
     */
    public function getProductId()
    {
        return $this->getParameter('productId');
    }

    /**
     * Set a merchant's product id.
     *
     * @param string $value
     * @return BaseAbstractRequest|AbstractRequest
     * @throws RuntimeException
     */
    public function setProductId($value)
    {
        return $this->setParameter('productId', $value);
    }

    /**
     * Get a secret word.
     *
     * @return string|null
     */
    public function getSecretWord()
    {
        return $this->getParameter('secretWord');
    }

    /**
     * @param string $value
     * @return BaseAbstractRequest
     * @throws RuntimeException
     */
    public function setSecretWord($value)
    {
        return $this->setParameter('secretWord', $value);
    }

    /**
     * Get a request token.
     *
     * @return string
     */
    public function getRequestToken()
    {
        return md5($this->getMerchantId() . $this->getProductId() . (float)$this->getAmount() . $this->getTransactionId() . $this->getSecretWord());
    }

    /**
     * Send the request with specified data
     *
     * @param  mixed $data The data to send
     * @return ResponseInterface
     */
    public function sendData($data)
    {
        $url = $this->getEndpoint();

        $options = array();

        if ($this->getTestMode()) {
            $options = array(
                'verify' => false,
            );
        }

        $httpResponse = $this->httpClient
            ->post($url, array(), $data, $options)
            ->send();

        $contents = $httpResponse->getBody(true);
        $xml = simplexml_load_string($contents);

        return $this->createResponse($xml);
    }

    /**
     * @return string
     */
    protected function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    /**
     * @param $data
     * @return Response
     */
    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }
}
