<?php

namespace Omnipay\AcquiroPay\Message;

use Guzzle\Http\EntityBody;
use Omnipay\Common\Message\AbstractResponse;

/**
 * Response
 */
class Response extends AbstractResponse
{
    /** @var EntityBody */
    protected $data;

    protected $decodedData;

    public function getData()
    {
        if ($this->decodedData === null) {
            /** @var EntityBody|string $data */
            $data = parent::getData();

            if ($data instanceof EntityBody) {
                $this->decodedData = json_decode(stream_get_contents($data->getStream()), true);
            } else {
                parse_str($data, $this->decodedData);
            }

            if ($this->decodedData === null) {
                $this->decodedData = array();
            }
        }

        return $this->decodedData;
    }

    public function isSuccessful()
    {
        $data = $this->getData();

        return isset($data['success']);
    }

    public function getTransactionReference()
    {
        $data = $this->getData();

        if (isset($data['reference'])) {
            return $data['reference'];
        }

        return null;
    }
}
