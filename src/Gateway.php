<?php

/**
 * AcquiroPay Gateway.
 */

namespace Omnipay\AcquiroPay;

use Omnipay\AcquiroPay\Message\AuthorizeRequest;
use Omnipay\AcquiroPay\Message\CaptureRequest;
use Omnipay\AcquiroPay\Message\CompleteAuthorizeRequest;
use Omnipay\AcquiroPay\Message\PurchaseRequest;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\RequestInterface;

/**
 * @method RequestInterface capture(array $options = array())
 * @method RequestInterface purchase(array $options = array())
 * @method RequestInterface completePurchase(array $options = array())
 * @method RequestInterface refund(array $options = array())
 * @method RequestInterface void(array $options = array())
 * @method RequestInterface createCard(array $options = array())
 * @method RequestInterface updateCard(array $options = array())
 * @method RequestInterface deleteCard(array $options = array())
 */
class Gateway extends AbstractGateway
{
    /**
     * Get gateway display name.
     *
     * This can be used by carts to get the display name for each gateway.
     *
     * @return string
     */
    public function getName()
    {
        return 'AcquiroPay';
    }

    /**
     * Get the gateway parameters.
     *
     * @return array
     */
    public function getDefaultParameters()
    {
        return array(
            'merchantId' => '',
            'productId'  => '',
            'secretWord' => '',
        );
    }

    /**
     * Get a merchant id.
     *
     * @return string
     */
    public function getMerchantId()
    {
        return $this->getParameter('merchantId');
    }

    /**
     * Set a merchant id.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setMerchantId($value)
    {
        return $this->setParameter('merchantId', $value);
    }

    /**
     * Get a merchant's product id.
     *
     * @return string
     */
    public function getProductId()
    {
        return $this->getParameter('productId');
    }

    /**
     * Set a merchant's product id.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setProductId($value)
    {
        return $this->setParameter('productId', $value);
    }

    /**
     * Get a secret word.
     *
     * @return string
     */
    public function getSecretWord()
    {
        return $this->getParameter('secretWord');
    }

    /**
     * Set a secret word.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setSecretWord($value)
    {
        return $this->setParameter('secretWord', $value);
    }

    /**
     * Authorize request.
     *
     * An Authorize request is similar to a purchase request but the
     * charge issues an authorization (or pre-authorization), and no money
     * is transferred.  The transaction will need to be captured later
     * in order to effect payment.
     *
     * @param array $options
     *
     * @return AuthorizeRequest|RequestInterface
     */
    public function authorize(array $options = array())
    {
        return $this->createRequest('\Omnipay\AcquiroPay\Message\AuthorizeRequest', $options);
    }

    /**
     * Handle return from off-site gateways after authorization.
     *
     * @param array $options
     *
     * @return CompleteAuthorizeRequest|RequestInterface
     */
    public function completeAuthorize(array $options = array())
    {
        return $this->createRequest('\Omnipay\AcquiroPay\Message\CompleteAuthorizeRequest', $options);
    }

    /**
     * Capture Request.
     *
     * Use this request to capture and process a previously created authorization.
     *
     * @param array $options
     *
     * @return CaptureRequest|RequestInterface
     */
    public function capture(array $options = array())
    {
        return $this->createRequest('\Omnipay\AcquiroPay\Message\CaptureRequest', $options);
    }

    /**
     * Purchase request.
     *
     * Authorize and immediately capture an amount on the customers card.
     *
     * @param array $options
     *
     * @return PurchaseRequest
     */
    public function purchase(array $options = array())
    {
        return $this->createRequest('\Omnipay\AcquiroPay\Message\PurchaseRequest', $options);
    }

    /**
     * Complete purchase request.
     *
     * Handle return from off-site gateways after purchase
     *
     * @param array $options
     *
     * @return RequestInterface|void
     */
    public function completePurchase(array $options = array())
    {
        return $this->createRequest('\Omnipay\AcquiroPay\Message\CompletePurchaseRequest', $options);
    }

    public function __call($name, $arguments)
    {
        // AUTHORIZE - 0
        // COMPLETE AUTHORIZE - 3
        // CAPTURE - 13

        // PURCHASE - 0
        // COMPLETE PURCHASE - 3

        // REFUND - 1
        // VOID - 1

        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface capture(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface purchase(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface completePurchase(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface refund(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface void(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface createCard(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = array())
        // TODO: Implement @method \Omnipay\Common\Message\RequestInterface deleteCard(array $options = array())
    }
}
