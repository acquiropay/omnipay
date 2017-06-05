<?php

namespace Omnipay\AcquiroPay\Tests;

use Omnipay\AcquiroPay\Message\AuthorizeRequest;
use Omnipay\Common\CreditCard;
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
        // Firstly, we test data without optional parameters
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

        $token = md5(getenv('MERCHANT_ID') . getenv('PRODUCT_ID') . '10' . 'foo' . getenv('SECRET'));

        $this->assertSame($token, $data['token']);

        // Ensure that optional parameters not in data array
        $this->assertArrayNotHasKey('cf2', $data);
        $this->assertArrayNotHasKey('cf3', $data);
        $this->assertArrayNotHasKey('cb_url', $data);

        // Now we set optional parameters and check them
        $this->request
            ->setCf2('cf2_foo')
            ->setCf3('cf3_foo')
            ->setCallbackUrl('https://example.com/callback');

        $data = $this->request->getData();

        $this->assertSame('cf2_foo', $data['cf2']);
        $this->assertSame('cf3_foo', $data['cf3']);
        $this->assertSame('https://example.com/callback', $data['cb_url']);
    }

    public function testSendSuccess()
    {
        $card = new CreditCard(array(
            'firstName' => 'CARD',
            'lastName' => 'HOLDER',
            'number' => '4000000000000002',
            'expiryMonth' => 12,
            'expiryYear' => '2999',
            'cvv' => 123,
        ));

        $this->request
            ->setTestMode(true)
            ->setAmount('10.00')
            ->setCard($card)
            ->setTransactionId($transactionId = uniqid())
            ->setClientIp('127.0.0.1');

        $response = $this->request->send();

        $data = $response->getData();

        $this->assertTrue($response->isSuccessful());
        $this->assertNotNull($response->getTransactionReference());
        $this->assertEquals('PURCHASE', $data['extended_status']);
        $this->assertEquals('PURCHASE', $data['transaction_status']);
    }
}
