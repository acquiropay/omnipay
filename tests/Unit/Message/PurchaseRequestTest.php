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

    public function testGetDataWithAppleReference()
    {
        // Firstly, we test data without optional parameters
        $this->request
            ->setAppleReference('foo')
            ->setMerchantId('merchant-1')
            ->setProductId('product-2')
            ->setSecretWord('secret-3')
            ->setAmount('10.00')
            ->setTransactionId('bar')
            ->setClientIp('127.0.0.1')
            ->setReturnUrl('http://merchant-site.app');

        $data = $this->request->getData();

        $token = md5('merchant-1'.'product-2'.'10.00'.'bar'.'secret-3');

        $expected = [
            'opcode'       => 4,
            'product_id'   => 'product-2',
            'apple_token'  => urlencode(base64_encode(json_encode('foo'))),
            'token'        => $token,
        ];

        $this->assertSame($expected, $data);

        // Ensure that optional parameters not in data array
        $this->assertArrayNotHasKey('phone', $data);
        $this->assertArrayNotHasKey('cf2', $data);
        $this->assertArrayNotHasKey('cf3', $data);
        $this->assertArrayNotHasKey('cb_url', $data);

        // Now we set optional parameters and check them
        $this->request
            ->setPhone('+74951234567')
            ->setCf2('cf2_foo')
            ->setCf3('cf3_foo')
            ->setCallbackUrl('https://merchant-site.app/callback');

        $data = $this->request->getData();

        $expected['phone'] = '+74951234567';
        $expected['cf2'] = 'cf2_foo';
        $expected['cf3'] = 'cf3_foo';
        $expected['cb_url'] = 'https://merchant-site.app/callback';
        $expected['token'] = md5('merchant-1'.'product-2'.'10.00'.'bar'.'+74951234567'.'secret-3');

        $this->assertSame($expected, $data);
    }

    public function testGetDataWithCardReference()
    {
        // Firstly, we test data without optional parameters
        $this->request
            ->setMerchantId('merchant-1')
            ->setProductId('product-2')
            ->setSecretWord('secret-3')
            ->setAmount('10.00')
            ->setCard($card = new CreditCard(['cvv' => rand(100, 999)]))
            ->setCardReference('foo')
            ->setTransactionId('bar')
            ->setClientIp('127.0.0.1')
            ->setReturnUrl('http://merchant-site.app');

        $data = $this->request->getData();

        $token = md5('merchant-1'.'product-2'.'10.00'.'bar'.'secret-3');

        $expected = [
            'opcode'      => 21,
            'product_id'  => 'product-2',
            'payment_id'  => 'foo',
            'amount'      => '10.00',
            'cf'          => 'bar',
            'ip_address'  => '127.0.0.1',
            'cvv'         => $card->getCvv(),
            'pp_identity' => 'card',
            'token'       => $token,
        ];

        $this->assertSame($expected, $data);

        // Ensure that optional parameters not in data array
        $this->assertArrayNotHasKey('phone', $data);
        $this->assertArrayNotHasKey('cf2', $data);
        $this->assertArrayNotHasKey('cf3', $data);
        $this->assertArrayNotHasKey('cb_url', $data);

        // Now we set optional parameters and check them
        $this->request
            ->setPhone('+74951234567')
            ->setCf2('cf2_foo')
            ->setCf3('cf3_foo')
            ->setCallbackUrl('https://merchant-site.app/callback');

        $data = $this->request->getData();

        $expected['phone'] = '+74951234567';
        $expected['cf2'] = 'cf2_foo';
        $expected['cf3'] = 'cf3_foo';
        $expected['cb_url'] = 'https://merchant-site.app/callback';
        $expected['token'] = md5('merchant-1'.'product-2'.'10.00'.'bar'.'+74951234567'.'secret-3');

        $this->assertSame($expected, $data);
    }

    public function testGetDataWithCard()
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

        $expected = [
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
        ];

        $this->assertSame($expected, $data);

        // Ensure that optional parameters not in data array
        $this->assertArrayNotHasKey('phone', $data);
        $this->assertArrayNotHasKey('cf2', $data);
        $this->assertArrayNotHasKey('cf3', $data);
        $this->assertArrayNotHasKey('cb_url', $data);

        // Now we set optional parameters and check them
        $this->request
            ->setPhone('+74951234567')
            ->setCf2('cf2_foo')
            ->setCf3('cf3_foo')
            ->setCallbackUrl('https://merchant-site.app/callback');

        $data = $this->request->getData();

        $expected['phone'] = '+74951234567';
        $expected['cf2'] = 'cf2_foo';
        $expected['cf3'] = 'cf3_foo';
        $expected['cb_url'] = 'https://merchant-site.app/callback';
        $expected['token'] = md5('merchant-1'.'product-2'.'10.00'.'foo'.'+74951234567'.'secret-3');

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

        $card = new CreditCard([
            'firstName'   => 'CARD',
            'lastName'    => 'HOLDER',
            'number'      => '4000000000000002',
            'expiryMonth' => 12,
            'expiryYear'  => '2999',
            'cvv'         => 123,
        ]);

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
            ['PaReq' => 'foo', 'MD' => 'bar', 'TermUrl' => 'http://merchant-site.app'],
            $response->getRedirectData()
        );

        $this->assertInstanceOf('\Symfony\Component\HttpFoundation\Response', $response->getRedirectResponse());
        $this->assertNotNull($response->getTransactionReference());
        $this->assertSame('3DSECURE', $response->getStatus());
    }

    public function testSendSuccess()
    {
        $this->setMockHttpResponse('PurchaseSuccess.txt');

        $card = new CreditCard([
            'firstName'   => 'CARD',
            'lastName'    => 'HOLDER',
            'number'      => '4000000000000002',
            'expiryMonth' => 12,
            'expiryYear'  => '2999',
            'cvv'         => 123,
        ]);

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

        $card = new CreditCard([
            'firstName'   => 'CARD',
            'lastName'    => 'HOLDER',
            'number'      => '4000000000000002',
            'expiryMonth' => 12,
            'expiryYear'  => '2999',
            'cvv'         => 123,
        ]);

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
        return [
            ['10.00'],
            ['10.50'],
            ['14500.00'],
            ['11500.53'],
        ];
    }
}
