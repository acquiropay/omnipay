<?php

namespace Omnipay\AcquiroPay\Message;


/**
 * Complete Authorize Request
 *
 * @method Response send()
 */
class CompleteAuthorizeRequest extends AbstractRequest
{
    /**
     * Get MD.
     *
     * @return string
     */
    public function getMD()
    {
        return $this->getParameter('MD');
    }

    /**
     * Set MD.
     *
     * @param string $value
     *
     * @return static|\Omnipay\Common\Message\AbstractRequest
     */
    public function setMD($value)
    {
        return $this->setParameter('MD', $value);
    }

    /**
     * Get PaRes.
     *
     * @return string
     */
    public function getPaRes()
    {
        return $this->getParameter('PaRes');
    }

    /**
     * Set PaRes.
     *
     * @param string $value
     *
     * @return static|\Omnipay\Common\Message\AbstractRequest
     */
    public function setPaRes($value)
    {
        return $this->setParameter('PaRes', $value);
    }


    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return array
     */
    public function getData()
    {
        $this->validate('transactionId', 'MD', 'PaRes');

        return array(
            'opcode' => 3,
            'payment_id' => $this->getTransactionId(),
            'PaRes' => $this->getPaRes(),
            'MD' => $this->getMD(),
            'token' => $this->getRequestToken(),
        );
    }

    /**
     * Get a request token.
     *
     * @return string
     */
    public function getRequestToken()
    {
        return md5(
            $this->getMerchantId()
            . $this->getTransactionId()
            . $this->getSecretWord()
        );
    }
}
