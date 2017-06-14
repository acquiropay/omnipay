<?php

namespace Omnipay\AcquiroPay;

use Omnipay\AcquiroPay\Message\AuthorizeRequest;
use Omnipay\AcquiroPay\Message\CaptureRequest;
use Omnipay\AcquiroPay\Message\CompleteAuthorizeRequest;
use Omnipay\AcquiroPay\Message\CompletePurchaseRequest;
use Omnipay\AcquiroPay\Message\PurchaseRequest;
use Omnipay\AcquiroPay\Message\RefundRequest;
use Omnipay\AcquiroPay\Message\StatusRequest;
use Omnipay\AcquiroPay\Message\VoidRequest;
use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\AbstractRequest;

/**
 * @method \Omnipay\Common\Message\RequestInterface createCard(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface updateCard(array $options = array())
 * @method \Omnipay\Common\Message\RequestInterface deleteCard(array $options = array())
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
        return [
            'merchantId' => '',
            'productId'  => '',
            'secretWord' => '',
        ];
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
     * @return AuthorizeRequest|AbstractRequest
     */
    public function authorize(array $options = [])
    {
        return $this->createRequest(AuthorizeRequest::class, $options);
    }

    /**
     * Handle return from off-site gateways after authorization.
     *
     * @param array $options
     *
     * @return CompleteAuthorizeRequest|AbstractRequest
     */
    public function completeAuthorize(array $options = [])
    {
        return $this->createRequest(CompleteAuthorizeRequest::class, $options);
    }

    /**
     * Capture Request.
     *
     * Use this request to capture and process a previously created authorization.
     *
     * @param array $options
     *
     * @return CaptureRequest|AbstractRequest
     */
    public function capture(array $options = [])
    {
        return $this->createRequest(CaptureRequest::class, $options);
    }

    /**
     * Purchase request.
     *
     * Authorize and immediately capture an amount on the customers card.
     *
     * @param array $options
     *
     * @return PurchaseRequest|AbstractRequest
     */
    public function purchase(array $options = [])
    {
        return $this->createRequest(PurchaseRequest::class, $options);
    }

    /**
     * Complete purchase request.
     *
     * Handle return from off-site gateways after purchase.
     *
     * @param array $options
     *
     * @return CompletePurchaseRequest|AbstractRequest
     */
    public function completePurchase(array $options = [])
    {
        return $this->createRequest(CompletePurchaseRequest::class, $options);
    }

    /**
     * Refund request.
     *
     * Refund an already processed transaction.
     *
     * @param array $options
     *
     * @return RefundRequest|AbstractRequest
     */
    public function refund(array $options = [])
    {
        return $this->createRequest(RefundRequest::class, $options);
    }

    /**
     * Void request.
     *
     * Generally can only be called up to 24 hours after submitting a transaction.
     *
     * @param array $options
     *
     * @return VoidRequest|AbstractRequest
     */
    public function void(array $options = [])
    {
        return $this->createRequest(VoidRequest::class, $options);
    }

    /**
     * Status request.
     *
     * @param array $options
     *
     * @return StatusRequest|AbstractRequest
     */
    public function status(array $options = [])
    {
        return $this->createRequest(StatusRequest::class, $options);
    }
}
