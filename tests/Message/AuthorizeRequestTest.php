<?php

namespace Omnipay\AcquiroPay\Tests;

use Omnipay\AcquiroPay\Message\AuthorizeRequest;
use Omnipay\Tests\TestCase;

class AuthorizeRequestTest extends TestCase
{
    /** @var AuthorizeRequest */
    private $request;

    public function setUp()
    {
        parent::setUp();

        $this->request = new AuthorizeRequest($this->getHttpClient(), $this->getHttpRequest());


        $this->request->setMerchantId(getenv('MERCHANT_ID'));
        $this->request->setProductId(getenv('PRODUCT_ID'));
        $this->request->setSecretWord(getenv('SECRET'));
    }

    public function testGetData()
    {
        $this->request
            ->setAmount('10.00')
            ->setCard($card = $this->getValidCard())
            ->setTransactionId('foo')
            ->setClientIp('127.0.0.1');

        $data = $this->request->getData();

        $this->assertSame(0, $data['opcode']);
        $this->assertSame(getenv('PRODUCT_ID'), $data['product_id']);
        $this->assertSame(10.00, $data['amount']);
        $this->assertSame('foo', $data['cf']);
        $this->assertSame('127.0.0.1', $data['ip_address']);
        $this->assertSame($card['number'], $data['pan']);
        $this->assertSame($card['firstName'] . ' ' . $card['lastName'], $data['cardholder']);
        $this->assertSame($card['expiryMonth'], $data['exp_month']);
        $this->assertSame($card['expiryYear'], $data['exp_year']);
        $this->assertSame($card['cvv'], $data['cvv']);
        $this->assertSame('card', $data['pp_identity']);

        $token = md5(getenv('MERCHANT_ID') . getenv('PRODUCT_ID') . '10.00' . 'foo' . getenv('SECRET'));

        $this->assertSame($token, $data['token']);
    }
}
