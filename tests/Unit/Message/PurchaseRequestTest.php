<?php

namespace Omnipay\AcquiroPay\Tests\Unit\Message;

use Omnipay\AcquiroPay\Message\PurchaseRequest;
use Omnipay\Common\CreditCard;
use Omnipay\Tests\TestCase;

class PurchaseRequestTest extends TestCase
{
    /** @var PurchaseRequest */
    private $request;

    public function setUp()
    {
        parent::setUp();

        $this->request = new PurchaseRequest($this->getHttpClient(), $this->getHttpRequest());
    }

    public function testGetData()
    {
        // Firstly, we test data without optional parameters
        $this->request
            ->setMerchantId('merchant-1')
            ->setProductId('product-2')
            ->setSecretWord('secret-3')
            ->setAmount('10.00')
            ->setCard($card = new CreditCard($this->getValidCard()))
            ->setTransactionId('foo')
            ->setClientIp('127.0.0.1')
            ->setReturnUrl('http://merchant-site.app');

        $data = $this->request->getData();

        $token = md5('merchant-1'.'product-2'.'10.00'.'foo'.'secret-3');

        $expected = array(
            'opcode'      => 0,
            'product_id'  => 'product-2',
            'amount'      => '10.00',
            'cf'          => 'foo',
            'ip_address'  => '127.0.0.1',
            'pan'         => $card->getNumber(),
            'cardholder'  => $card->getName(),
            'exp_month'   => $card->getExpiryMonth(),
            'exp_year'    => $card->getExpiryYear(),
            'cvv'         => $card->getCvv(),
            'pp_identity' => 'card',
            'token'       => $token,
        );

        $this->assertSame($expected, $data);

        // Ensure that optional parameters not in data array
        $this->assertArrayNotHasKey('cf2', $data);
        $this->assertArrayNotHasKey('cf3', $data);
        $this->assertArrayNotHasKey('cb_url', $data);

        // Now we set optional parameters and check them
        $this->request
            ->setCf2('cf2_foo')
            ->setCf3('cf3_foo')
            ->setCallbackUrl('https://merchant-site.app/callback');

        $data = $this->request->getData();

        $expected['cf2'] = 'cf2_foo';
        $expected['cf3'] = 'cf3_foo';
        $expected['cb_url'] = 'https://merchant-site.app/callback';

        $this->assertSame($expected, $data);
    }

    /**
     * @dataProvider amounts
     *
     * @param $amount
     */
    public function testGetRequestToken($amount)
    {
        $merchantId = mt_rand(1, 1000);
        $productId = mt_rand(1, 1000);
        $secretWord = uniqid();
        $transactionId = uniqid();

        $this->request
            ->setMerchantId($merchantId)
            ->setProductId($productId)
            ->setSecretWord($secretWord)
            ->setAmount($amount)
            ->setTransactionId($transactionId);

        $token = md5($merchantId.$productId.$amount.$transactionId.$secretWord);

        $this->assertSame($token, $this->request->getRequestToken());
    }

    public function testSend3DSecureSuccess()
    {
        $this->setMockHttpResponse('Purchase3DSecureSuccess.txt');

        $card = new CreditCard(array(
            'firstName'   => 'CARD',
            'lastName'    => 'HOLDER',
            'number'      => '4000000000000002',
            'expiryMonth' => 12,
            'expiryYear'  => '2999',
            'cvv'         => 123,
        ));

        $this->request
            ->setTestMode(true)
            ->setAmount('10.00')
            ->setCard($card)
            ->setTransactionId($transactionId = uniqid())
            ->setClientIp('127.0.0.1')
            ->setReturnUrl('http://merchant-site.app');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertSame(
            'https://3dstest.mdmbank.ru/way4acs/pa?id=WTa6m3WZTTo6Ry9ZBdq9ig',
            $response->getRedirectUrl()
        );
        $this->assertSame('POST', $response->getRedirectMethod());
        $this->assertSame(
            array('PaReq' => 'foo', 'MD' => 'bar', 'TermUrl' => 'http://merchant-site.app'),
            $response->getRedirectData()
        );

        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response->getRedirectResponse());
        $this->assertNotNull($response->getTransactionReference());
        $this->assertSame('3DSECURE', $response->getStatus());
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');

        $card = new CreditCard(array(
            'firstName'   => 'CARD',
            'lastName'    => 'HOLDER',
            'number'      => '4000000000000002',
            'expiryMonth' => 12,
            'expiryYear'  => '2999',
            'cvv'         => 123,
        ));

        $this->request
            ->setTestMode(true)
            ->setAmount('10.00')
            ->setCard($card)
            ->setTransactionId($transactionId = uniqid())
            ->setClientIp('127.0.0.1')
            ->setReturnUrl('http://merchant-site.app');

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertNotNull($response->getTransactionReference());
        $this->assertSame('PURCHASE', $response->getStatus());
    }

    public function testSendError()
    {
        $this->setMockHttpResponse('PurchaseFailure.txt');

        $card = new CreditCard(array(
            'firstName'   => 'CARD',
            'lastName'    => 'HOLDER',
            'number'      => '4000000000000002',
            'expiryMonth' => 12,
            'expiryYear'  => '2999',
            'cvv'         => 123,
        ));

        $this->request
            ->setTestMode(true)
            ->setAmount('10.00')
            ->setCard($card)
            ->setTransactionId($transactionId = uniqid())
            ->setClientIp('127.0.0.1')
            ->setReturnUrl('http://merchant-site.app');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('DECLINE', $response->getStatus());
    }

    public function amounts()
    {
        return array(
            array('10.00'),
            array('10.50'),
            array('14500.00'),
            array('11500.53'),
        );
    }
}
