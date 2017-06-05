<?php

namespace Omnipay\AcquiroPay\Tests;

use Omnipay\AcquiroPay\Message\CompleteAuthorizeRequest;
use Omnipay\Tests\TestCase;

class CompleteAuthorizeRequestTest extends TestCase
{
    /** @var CompleteAuthorizeRequest */
    private $request;

    /** @var int */
    private $paymentId;
    /** @var string */
    private $paRes;
    /** @var string */
    private $md;
    /** @var int */
    private $merchantId;
    /** @var string */
    private $secretWord;

    public function setUp()
    {
        $this->paymentId = mt_rand(1, 100);
        $this->paRes = uniqid('test_', true);
        $this->md = uniqid('test_', true);
        $this->merchantId = (int)getenv('MERCHANT_ID');
        $this->merchantId = empty($this->merchantId) ? mt_rand(1, 100) : $this->merchantId;
        $this->secretWord = getenv('SECRET');
        $this->secretWord = empty($this->secretWord) ? uniqid('test_', true) : $this->secretWord;

        $httpRequest = $this->getHttpRequest();
        $httpRequest->query->set('payment_id', $this->paymentId);
        $httpRequest->query->set('PaRes', $this->paRes);
        $httpRequest->query->set('MD', $this->md);

        $this->request = new CompleteAuthorizeRequest($this->getHttpClient(), $httpRequest);
        $this->request->setMerchantId($this->merchantId);
        $this->request->setSecretWord($this->secretWord);
    }

    public function testGetData()
    {
        $expected = array(
            'opcode' => 3,
            'payment_id' => $this->paymentId,
            'PaRes' => $this->paRes,
            'MD' => $this->md,
            'token' => $this->request->getRequestToken(),
        );
        $this->assertSame($expected, $this->request->getData());
    }
}