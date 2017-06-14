<?php

namespace Omnipay\AcquiroPay\Tests\Unit\Message;

use Omnipay\AcquiroPay\Message\VoidRequest;
use Omnipay\Tests\TestCase;

class VoidRequestTest extends TestCase
{
    /** @var VoidRequest */
    private $request;

    protected function setUp()
    {
        $this->request = new VoidRequest($this->getHttpClient(), $this->getHttpRequest());
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
        $this->setMockHttpResponse('VoidSuccess.txt');

        $this->request->setTransactionReference('foo');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('e014b11a43a840e599f3b51f6900d0c9', $response->getTransactionReference());
        $this->assertSame('REFUND', $response->getStatus());
    }

    public function testSendFailure()
    {
        $this->setMockHttpResponse('VoidFailure.txt');

        $this->request->setTransactionReference('foo');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('Cancellation is unavailable', $response->getMessage());
    }
}
