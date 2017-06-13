<?php

namespace Omnipay\AcquiroPay\Message;

/**
 * Authorize Request.
 *
 * @method Response send()
 */
class AuthorizeRequest extends AbstractRequest
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
     * @return static|\Omnipay\Common\Message\AbstractRequest
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
     * @return static|\Omnipay\Common\Message\AbstractRequest
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
     * @return static|\Omnipay\Common\Message\AbstractRequest
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
     * @return static|\Omnipay\Common\Message\AbstractRequest
     */
    public function setCallbackUrl($value)
    {
        return $this->setParameter('callbackUrl', $value);
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return array
     */
    public function getData()
    {
        $this->validate(
            'amount',
            'card',
            'transactionId',
            'clientIp',
            'productId'
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
