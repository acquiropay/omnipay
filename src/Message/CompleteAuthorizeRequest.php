<?php

namespace Omnipay\AcquiroPay\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Complete Authorize Request
 *
 * @method Response send()
 */
class CompleteAuthorizeRequest extends AbstractRequest
{
    /**
     * @return mixed|array
     * @throws InvalidRequestException
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
