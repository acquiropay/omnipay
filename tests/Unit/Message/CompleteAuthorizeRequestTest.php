<?php

namespace Omnipay\AcquiroPay\Tests\Unit\Message;

use Omnipay\AcquiroPay\Message\CompleteAuthorizeRequest;
use Omnipay\Tests\TestCase;

class CompleteAuthorizeRequestTest extends TestCase
{
    /** @var CompleteAuthorizeRequest */
    private $request;

    public function setUp()
    {
        $this->request = new CompleteAuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetData()
    {
        $md = uniqid('test_', true);
        $paRes = uniqid('test_', true);
        $transactionReference = mt_rand(1, 100);

        $this->request
            ->setMD($md)
            ->setPaRes($paRes)
            ->setTransactionReference($transactionReference);

        $expected = [
            'opcode'     => 3,
            'payment_id' => $transactionReference,
            'PaRes'      => $paRes,
            'MD'         => $md,
            'token'      => $this->request->getRequestToken(),
        ];

        $this->assertSame($expected, $this->request->getData());
    }

    public function testGetRequestToken()
    {
        $merchantId = mt_rand(1, 1000);
        $secretWord = uniqid();
        $transactionReference = uniqid();

        $this->request
            ->setMerchantId($merchantId)
            ->setSecretWord($secretWord)
            ->setTransactionReference($transactionReference);

        $token = md5($merchantId.$transactionReference.$secretWord);

        $this->assertSame($token, $this->request->getRequestToken());
    }

    public function testSendPreauthorizationSuccess()
    {
        $this->setMockHttpResponse('CompleteAuthorizePreauthorizationSuccess.txt');

        $this->request
            ->setMD('foo')
            ->setPaRes('bar')
            ->setTransactionReference(mt_rand(1, 100));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('PREAUTHORIZATION', $response->getStatus());
    }
}
