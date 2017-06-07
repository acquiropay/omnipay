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
            'amount' => '10.00',
            'card' => new CreditCard($this->getValidCard()),
            'transactionId' => uniqid('test_', true),
            'clientIp' => '127.0.0.1',
        );

        $request = $this->gateway->authorize($options);

        $this->assertInstanceOf('Omnipay\AcquiroPay\Message\AuthorizeRequest', $request);
        $this->assertSame($options['amount'], $request->getAmount());
    }

    public function testStatusParameters()
    {
        foreach ($this->gateway->getDefaultParameters() as $key => $default) {
            // set property on gateway
            $getter = 'get' . ucfirst($this->camelCase($key));
            $setter = 'set' . ucfirst($this->camelCase($key));
            $value = uniqid();
            $this->gateway->$setter($value);

            // request should have matching property, with correct value
            $request = $this->gateway->status();
            $this->assertSame($value, $request->$getter());
        }
    }
}
