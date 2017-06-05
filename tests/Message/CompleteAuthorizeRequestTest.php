<?php

namespace Omnipay\AcquiroPay\Tests;

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
        $paymentId = mt_rand(1, 100);

        $this->request
            ->setMD($md)
            ->setPaRes($paRes)
            ->setTransactionId($paymentId);

        $expected = array(
            'opcode' => 3,
            'payment_id' => $paymentId,
            'PaRes' => $paRes,
            'MD' => $md,
            'token' => $this->request->getRequestToken(),
        );
        $this->assertSame($expected, $this->request->getData());
    }
}