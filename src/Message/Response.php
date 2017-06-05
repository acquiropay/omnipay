<?php

namespace Omnipay\AcquiroPay\Message;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Response
 */
class Response extends AbstractResponse implements RedirectResponseInterface
{
    public function getData()
    {
        $parent = parent::getData();

        return json_decode(json_encode((array)$parent), true);
    }

    /**
     * Is the response successful?
     *
     * @return boolean
     */
    public function isSuccessful()
    {
        $data = $this->getData();

        return isset($data['status']) && $data['status'] !== 'KO';
    }

    /**
     * Does the response require a redirect?
     *
     * @return boolean
     */
    public function isRedirect()
    {
        $request = $this->request->getData();
        $response = $this->getData();

        return
            $request['opcode'] === 0 &&
            $response['extended_status'] === '3DSECURE';
    }

    /**
     * Gateway Reference
     *
     * @return null|string A reference provided by the gateway to represent this transaction
     */
    public function getTransactionReference()
    {
        $data = $this->getData();

        if (isset($data['extended_id'])) {
            return $data['extended_id'];
        }

        return null;
    }


    /**
     * Gets the redirect target url.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        $data = $this->getData();

        return $data['additional']['secure3d']['auth-form'];
    }

    /**
     * Get the required redirect method (either GET or POST).
     *
     * @return string
     */
    public function getRedirectMethod()
    {
        $data = $this->getData();

        return $data['additional']['secure3d']['auth-form-method'];
    }

    /**
     * Gets the redirect form data array, if the redirect method is POST.
     *
     * @return array
     */
    public function getRedirectData()
    {
        $data = $this->getData();

        return array(
            'PaReq' => $data['additional']['secure3d']['retransmit']['PaReq'],
            'MD' => $data['additional']['secure3d']['retransmit']['MD'],
        );
    }
}
