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
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return array
     */
    public function getData()
    {
        return array(
            'opcode' => 3,
            'payment_id' => $this->httpRequest->get('payment_id'),
            'PaRes' => $this->httpRequest->get('PaRes'),
            'MD' => $this->httpRequest->get('MD'),
            'token' => $this->getRequestToken(),
        );
    }

    public function getRequestToken()
    {
        return md5(
            $this->getMerchantId()
            . $this->httpRequest->get('payment_id')
            . $this->getSecretWord()
        );
    }
}
