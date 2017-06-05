<?php

namespace Omnipay\AcquiroPay\Message;

/**
 * Authorize Request
 *
 * @method Response send()
 */
class AuthorizeRequest extends AbstractRequest
{
    public function getCf2()
    {
        return $this->getParameter('cf2');
    }

    /**
     * @param $value
     *
     * @return static|\Omnipay\Common\Message\AbstractRequest
     */
    public function setCf2($value)
    {
        return $this->setParameter('cf2', $value);
    }

    public function getCf3()
    {
        return $this->getParameter('cf3');
    }

    /**
     * @param $value
     *
     * @return static|\Omnipay\Common\Message\AbstractRequest
     */
    public function setCf3($value)
    {
        return $this->setParameter('cf3', $value);
    }

    public function getCallbackUrl()
    {
        return $this->getParameter('callbackUrl');
    }

    /**
     * @param $value
     *
     * @return static|\Omnipay\Common\Message\AbstractRequest
     */
    public function setCallbackUrl($value)
    {
        return $this->setParameter('callbackUrl', $value);
    }

    public function getData()
    {
        $this->validate(
            'amount',
            'card',
            'transactionId',
            'clientIp'
        );

        $card = $this->getCard();

        $card->validate();

        $data = array(
            'opcode' => 0,
            'product_id' => $this->getProductId(),
            'amount' => (float)$this->getAmount(),
            'cf' => $this->getTransactionId(),
            'ip_address' => $this->getClientIp(),
            'pan' => $card->getNumber(),
            'cardholder' => $card->getName(),
            'exp_month' => $card->getExpiryMonth(),
            'exp_year' => $card->getExpiryYear(),
            'cvv' => $card->getCvv(),
            'pp_identity' => 'card',
            'token' => $this->getRequestToken(),
        );

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
}
