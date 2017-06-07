<?php

declare(strict_types=1);

namespace Omnipay\AcquiroPay\Message;

/**
 * Refund Request.
 *
 * @method Response send()
 */
class RefundRequest extends AbstractRequest
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

        $data = array(
            'opcode'     => 1,
            'payment_id' => $this->getTransactionReference(),
            'token'      => $this->getRequestToken(),
        );

        if ($this->getAmount()) {
            $data['amount'] = $this->getAmount();
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
        return md5($this->getMerchantId().$this->getTransactionReference().$this->getAmount().$this->getSecretWord());
    }
}
