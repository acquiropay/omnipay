<?php

namespace Omnipay\AcquiroPay\Tests\Unit\Message;

use Omnipay\AcquiroPay\Message\RefundRequest;
use Omnipay\Tests\TestCase;

class RefundRequestTest extends TestCase
{
    /** @var RefundRequest */
    private $request;

    protected function setUp()
    {
        $this->request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetData()
    {
        $this->request->setTransactionReference('foo');

        $expected = [
            'opcode'     => 1,
            'payment_id' => 'foo',
            'token'      => $this->request->getRequestToken(),
        ];

        $this->assertSame($expected, $this->request->getData());
    }

    public function testGetRequestToken()
    {
        $this->request
            ->setMerchantId('merchant-1')
            ->setSecretWord('secret-2')
            ->setTransactionReference('foo')
            ->setAmount('100.50');

        $token = md5('merchant-1'.'foo'.'100.50'.'secret-2');

        $this->assertSame($token, $this->request->getRequestToken());
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('RefundSuccess.txt');

        $this->request
            ->setTransactionReference('foo')
            ->setAmount('15.50');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('43314449d66a4ab1bc474aaca4171d5a', $response->getTransactionReference());
        $this->assertSame('REFUND', $response->getStatus());
    }

    public function testSendError()
    {
        $this->setMockHttpResponse('RefundFailure.txt');

        $this->request->setTransactionReference('foo');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('Cancellation is unavailable', $response->getMessage());
    }
}
