<?php

namespace Omnipay\AcquiroPay\Message;

use Omnipay\Common\Message\AbstractResponse;

/**
 * Response
 */
class Response extends AbstractResponse
{
    public function getData()
    {
        $parent = parent::getData();

        return json_decode(json_encode((array)$parent), true);
    }

    public function isSuccessful()
    {
        $data = $this->getData();

        return isset($data['status']) && $data['status'] !== 'KO';
    }

    public function getTransactionReference()
    {
        $data = $this->getData();

        if (isset($data['extended_id'])) {
            return $data['extended_id'];
        }

        return null;
    }
}
