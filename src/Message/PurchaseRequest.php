<?php

namespace Omnipay\AcquiroPay\Message;

use Omnipay\Common\Message\AbstractRequest;

/**
 * Purchase Request.
 *
 * @method Response send()
 */
class PurchaseRequest extends AuthorizeRequest
{
    /**
     * Get phone.
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->getParameter('phone');
    }

    /**
     * Set phone.
     *
     * @param string $value
     *
     * @return static|AbstractRequest
     */
    public function setPhone($value)
    {
        return $this->setParameter('phone', $value);
    }

    /**
     * Get custom field 2.
     *
     * @return string
     */
    public function getCf2()
    {
        return $this->getParameter('cf2');
    }

    /**
     * Set custom field 2.
     *
     * @param string $value
     *
     * @return static|AbstractRequest
     */
    public function setCf2($value)
    {
        return $this->setParameter('cf2', $value);
    }

    /**
     * Get custom field 3.
     *
     * @return string
     */
    public function getCf3()
    {
        return $this->getParameter('cf3');
    }

    /**
     * Set custom field 3.
     *
     * @param string $value
     *
     * @return static|AbstractRequest
     */
    public function setCf3($value)
    {
        return $this->setParameter('cf3', $value);
    }

    /**
     * Get callback URL.
     *
     * @return string
     */
    public function getCallbackUrl()
    {
        return $this->getParameter('callbackUrl');
    }

    /**
     * Set callback URL.
     *
     * @param string $value
     *
     * @return static|AbstractRequest
     */
    public function setCallbackUrl($value)
    {
        return $this->setParameter('callbackUrl', $value);
    }

    /**
     * Get apple reference.
     *
     * @return string
     */
    public function getAppleReference()
    {
        return $this->getParameter('appleReference');
    }

    /**
     * Set apple reference.
     *
     * @param string $value
     *
     * @return static|AbstractRequest
     */
    public function setAppleReference($value)
    {
        return $this->setParameter('appleReference', $value);
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return array
     */
    public function getData()
    {
        if ($this->getAppleReference() !== null) {
            $this->validate('amount', 'appleReference');

            $data = array(
                'opcode'      => 4,
                'product_id'  => $this->getProductId(),
                'apple_token' => urlencode(base64_encode(json_encode($this->getAppleReference()))),
                'token'       => $this->getRequestToken(),
            );
        } elseif ($this->getCardReference() !== null) {
            $this->validate(
                'amount',
                'card',
                'cardReference',
                'transactionId',
                'clientIp',
                'returnUrl'
            );

            $data = array(
                'opcode'      => 21,
                'product_id'  => $this->getProductId(),
                'payment_id'  => $this->getCardReference(),
                'amount'      => $this->getAmount(),
                'cf'          => $this->getTransactionId(),
                'ip_address'  => $this->getClientIp(),
                'cvv'         => $this->getCard()->getCvv(),
                'pp_identity' => 'card',
                'token'       => $this->getRequestToken(),
            );
        } else {
            $this->validate(
                'amount',
                'card',
                'transactionId',
                'clientIp',
                'returnUrl'
            );

            $card = $this->getCard();

            $card->validate();

            $data = array(
                'opcode'      => 0,
                'product_id'  => $this->getProductId(),
                'amount'      => $this->getAmount(),
                'cf'          => $this->getTransactionId(),
                'ip_address'  => $this->getClientIp(),
                'pan'         => $card->getNumber(),
                'cardholder'  => $card->getName(),
                'exp_month'   => $card->getExpiryMonth(),
                'exp_year'    => $card->getExpiryYear(),
                'cvv'         => $card->getCvv(),
                'pp_identity' => 'card',
                'token'       => $this->getRequestToken(),
            );
        }

        if ($this->getPhone()) {
            $data['phone'] = $this->getPhone();
        }
        if ($this->getCf2()) {
            $data['cf2'] = $this->getCf2();
        }
        if ($this->getCf3()) {
            $data['cf3'] = $this->getCf3();
        }
        if ($this->getCallbackUrl()) {
            $data['cb_url'] = $this->getCallbackUrl();
        }

        return $data;
    }

    /**
     * Get a request token.
     *
     * @return string
     */
    public function getRequestToken()
    {
        return md5($this->getMerchantId().$this->getProductId().$this->getAmount().$this->getTransactionId().$this->getPhone().$this->getSecretWord());
    }
}
