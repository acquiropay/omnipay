<?php

namespace Omnipay\AcquiroPay\Message;

/**
 * Status Request.
 *
 * @method Response send()
 */
class StatusRequest extends AbstractRequest
{
    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('transactionReference');

        return array(
            'opcode'     => 2,
            'payment_id' => $this->getTransactionReference(),
            'token'      => $this->getRequestToken(),
        );
    }

    /**
     * Get a request token.
     *
     * @return string
     */
    public function getRequestToken()
    {
        return md5($this->getMerchantId().$this->getTransactionReference().$this->getSecretWord());
    }
}
