<?php

namespace Omnipay\AcquiroPay\Tests\Unit\Message;

use Omnipay\AcquiroPay\Message\StatusRequest;
use Omnipay\Tests\TestCase;

class StatusRequestTest extends TestCase
{
    /** @var StatusRequest */
    private $request;

    public function setUp()
    {
        parent::setUp();

        $this->request = new StatusRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetData()
    {
        $this->request->setTransactionReference('foo');

        $expected = array(
            'opcode'     => 2,
            'payment_id' => 'foo',
            'token'      => $this->request->getRequestToken(),
        );

        $this->assertSame($expected, $this->request->getData());
    }

    public function testGetRequestToken()
    {
        $this->request
            ->setMerchantId('merchant-1')
            ->setSecretWord('secret-2')
            ->setTransactionReference('foo');

        $token = md5('merchant-1'.'foo'.'secret-2');

        $this->assertSame($token, $this->request->getRequestToken());
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('StatusSuccess.txt');

        $this->request->setTransactionReference('foo');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('e014b11a43a840e599f3b51f6900d0c9', $response->getTransactionReference());
        $this->assertSame('REFUND', $response->getStatus());
    }
}
