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
        $this->validate(
            'amount',
            'card',
            'transactionId',
            'clientIp'
        );

        $card = $this->getCard();

        $card->validate();

        return array(
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
    }
}
