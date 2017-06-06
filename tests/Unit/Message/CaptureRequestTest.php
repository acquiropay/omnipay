<?php

declare(strict_types=1);

namespace Omnipay\AcquiroPay\Tests\Unit\Message;

use Omnipay\AcquiroPay\Message\CaptureRequest;
use Omnipay\Tests\TestCase;

class CaptureRequestTest extends TestCase
{
    /** @var CaptureRequest */
    private $request;

    protected function setUp()
    {
        parent::setUp();

        $this->request = new CaptureRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetData()
    {
        $this->request->setTransactionId('foo');

        $data = $this->request->getData();

        $expected = array(
            'opcode' => 13,
            'payment_id' => 'foo',
            'token' => $this->request->getRequestToken(),
        );

        $this->assertSame($expected, $data);
    }

    public function testGetRequestToken()
    {
        $merchantId = mt_rand(1, 1000);
        $secretWord = uniqid();
        $transactionId = uniqid();

        $this->request
            ->setMerchantId($merchantId)
            ->setSecretWord($secretWord)
            ->setTransactionId($transactionId);

        $token = md5($merchantId . $transactionId . $secretWord);

        $this->assertSame($token, $this->request->getRequestToken());
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('CaptureSuccess.txt');

        $this->request->setTransactionId('foo');

        $response = $this->request->send();
        $data = $response->getData();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('4e95257132434fbf9bc0f19eaf08cffa', $response->getTransactionReference());
        $this->assertSame('CAPTURE', $data['extended_status']);
    }

    public function testSendFailure()
    {
        $this->setMockHttpResponse('CaptureFailure.txt');

        $this->request->setTransactionId('foo');

        $response = $this->request->send();
        $data = $response->getData();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('4e95257132434fbf9bc0f19eaf08cffa', $response->getTransactionReference());
        $this->assertSame('DECLINE', $data['extended_status']);
    }
}