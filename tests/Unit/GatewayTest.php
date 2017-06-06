<?php

namespace Omnipay\AcquiroPay\Tests\Unit;

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
            'amount'        => '10.00',
            'card'          => new CreditCard($this->getValidCard()),
            'transactionId' => uniqid('test_', true),
            'clientIp'      => '127.0.0.1',
        );

        $request = $this->gateway->authorize($options);

        $this->assertInstanceOf('Omnipay\AcquiroPay\Message\AuthorizeRequest', $request);
        $this->assertSame($options['amount'], $request->getAmount());
    }
}
