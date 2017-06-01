<?php

namespace Omnipay\AcquiroPay\Tests;

use Omnipay\AcquiroPay\Gateway;
use Omnipay\Common\CreditCard;
use Omnipay\Tests\GatewayTestCase;

class GatewayTest extends GatewayTestCase
{
    /** @var Gateway */
    protected $gateway;

    public function setUp()
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testAuthorize()
    {
        $options = array(
            'amount' => '10.00',
            'card' => new CreditCard(array(
                'firstName' => 'Example',
                'lastName' => 'User',
                'number' => '4111111111111111',
                'expiryMonth' => '12',
                'expiryYear' => '2020',
                'cvv' => '123',
            )),
            'transactionId' => uniqid(),
            'clientIp' => '127.0.0.1',
        );

        $request = $this->gateway->authorize($options);

        $this->assertInstanceOf('Omnipay\AcquiroPay\Message\AuthorizeRequest', $request);
    }
}
