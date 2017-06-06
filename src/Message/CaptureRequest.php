<?php

declare(strict_types=1);

namespace Omnipay\AcquiroPay\Message;

/**
 * Capture Request
 *
 * @method Response send()
 */
class CaptureRequest extends AbstractRequest
{

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     */
    public function getData()
    {
        $this->validate('transactionId');

        return array(
            'opcode' => 13,
            'payment_id' => $this->getTransactionId(),
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