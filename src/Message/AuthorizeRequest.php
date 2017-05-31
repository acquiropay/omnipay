<?php

namespace Omnipay\AcquiroPay\Message;

/**
 * Authorize Request
 *
 * @method Response send()
 */
class AuthorizeRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('amount', 'card');
        $this->getCard()->validate();

        return array(
            'transaction_id' => $this->getTransactionId(),
            'amount' => $this->getAmountInteger(),
            'currency' => strtolower($this->getCurrency()),
            'description' => $this->getDescription(),
            'metadata' => $this->getMetadata(),
        );
    }
}
