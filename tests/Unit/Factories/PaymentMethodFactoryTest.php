<?php

declare(strict_types=1);

namespace Tests\Unit\Factories;

use App\Contracts\PaymentMethodInterface;
use App\Factories\PaymentMethodFactory;
use App\Services\Payment\Adapters\FastPayAdapter;
use App\Services\Payment\CreditCardPayment;
use App\Services\Payment\PayPalPayment;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PaymentMethodFactoryTest extends TestCase
{
    /**
     * Test creating credit card payment
     */
    public function testCreateCreditCardPayment(): void
    {
        $payment = PaymentMethodFactory::create('credit_card', '4532015112830366');

        $this->assertInstanceOf(PaymentMethodInterface::class, $payment);
        $this->assertInstanceOf(CreditCardPayment::class, $payment);
    }

    /**
     * Test creating PayPal payment
     */
    public function testCreatePayPalPayment(): void
    {
        $payment = PaymentMethodFactory::create('paypal', 'user@example.com');

        $this->assertInstanceOf(PaymentMethodInterface::class, $payment);
        $this->assertInstanceOf(PayPalPayment::class, $payment);
    }

    /**
     * Test creating FastPay payment through adapter
     */
    public function testCreateFastPayPayment(): void
    {
        $payment = PaymentMethodFactory::create('fast_pay', 'fp_customer_12345678');

        $this->assertInstanceOf(PaymentMethodInterface::class, $payment);
        $this->assertInstanceOf(FastPayAdapter::class, $payment);
        $this->assertSame('FastPay', $payment->getName());
    }

    /**
     * Test create credit card directly
     */
    public function testCreateCreditCardDirectly(): void
    {
        $payment = PaymentMethodFactory::createCreditCardPayment('4532015112830366');

        $this->assertInstanceOf(CreditCardPayment::class, $payment);
    }

    /**
     * Test create PayPal directly
     */
    public function testCreatePayPalDirectly(): void
    {
        $payment = PaymentMethodFactory::createPayPalPayment('user@example.com');

        $this->assertInstanceOf(PayPalPayment::class, $payment);
    }

    /**
     * Test create FastPay adapter directly
     */
    public function testCreateFastPayDirectly(): void
    {
        $payment = PaymentMethodFactory::createFastPayPayment('fp_customer_12345678');

        $this->assertInstanceOf(FastPayAdapter::class, $payment);
    }

    /**
     * Test case insensitivity
     */
    public function testCreateIsCaseInsensitive(): void
    {
        $payment1 = PaymentMethodFactory::create('CREDIT_CARD', '4532015112830366');
        $payment2 = PaymentMethodFactory::create('Credit_Card', '4532015112830366');
        $payment3 = PaymentMethodFactory::create('FAST_PAY', 'fp_customer_12345678');

        $this->assertInstanceOf(CreditCardPayment::class, $payment1);
        $this->assertInstanceOf(CreditCardPayment::class, $payment2);
        $this->assertInstanceOf(FastPayAdapter::class, $payment3);
    }

    /**
     * Test empty credential throws exception
     */
    public function testEmptyCredentialThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Payment credential cannot be empty');

        PaymentMethodFactory::create('credit_card', '');
    }

    /**
     * Test invalid credit card number throws exception
     */
    public function testInvalidCreditCardThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid credit card number format');

        PaymentMethodFactory::create('credit_card', 'invalid');
    }

    /**
     * Test invalid PayPal email throws exception
     */
    public function testInvalidPayPalEmailThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid PayPal email address');

        PaymentMethodFactory::create('paypal', 'not-an-email');
    }

    /**
     * Test invalid FastPay token throws exception
     */
    public function testInvalidFastPayTokenThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid FastPay customer token');

        PaymentMethodFactory::create('fast_pay', 'invalid-token');
    }

    /**
     * Test unknown payment method throws exception
     */
    public function testUnknownPaymentMethodThrowsException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown payment method: bitcoin');

        PaymentMethodFactory::create('bitcoin', 'address123');
    }

    /**
     * Test valid credit card formats
     */
    public function testValidCreditCardFormats(): void
    {
        $validCards = [
            '4532015112830366',
            '378282246310005',
            '5425233010103442',
            '4111111111111111',
            '5500005555555559',
        ];

        foreach ($validCards as $card) {
            $payment = PaymentMethodFactory::create('credit_card', $card);
            $this->assertInstanceOf(CreditCardPayment::class, $payment);
        }
    }

    /**
     * Test credit card with spaces and dashes
     */
    public function testCreditCardWithFormattingCharacters(): void
    {
        $payment1 = PaymentMethodFactory::create('credit_card', '4532-0151-1283-0366');
        $payment2 = PaymentMethodFactory::create('credit_card', '4532 0151 1283 0366');

        $this->assertInstanceOf(CreditCardPayment::class, $payment1);
        $this->assertInstanceOf(CreditCardPayment::class, $payment2);
    }

    /**
     * Test valid PayPal emails
     */
    public function testValidPayPalEmails(): void
    {
        $validEmails = [
            'user@example.com',
            'john.doe@company.org',
            'test+tag@domain.co.uk',
        ];

        foreach ($validEmails as $email) {
            $payment = PaymentMethodFactory::create('paypal', $email);
            $this->assertInstanceOf(PayPalPayment::class, $payment);
        }
    }

    /**
     * Test from config array
     */
    public function testFromConfigArray(): void
    {
        $config = [
            'type' => 'credit_card',
            'credential' => '4532015112830366'
        ];
        $payment = PaymentMethodFactory::fromConfig($config);

        $this->assertInstanceOf(CreditCardPayment::class, $payment);
    }

    /**
     * Test from config missing type
     */
    public function testFromConfigMissingType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing payment method type');

        PaymentMethodFactory::fromConfig(['credential' => 'somevalue']);
    }

    /**
     * Test from config missing credential
     */
    public function testFromConfigMissingCredential(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Missing payment credential');

        PaymentMethodFactory::fromConfig(['type' => 'credit_card']);
    }

    /**
     * Test constants are available
     */
    public function testConstantsAvailable(): void
    {
        $this->assertEquals('credit_card', PaymentMethodFactory::CREDIT_CARD);
        $this->assertEquals('paypal', PaymentMethodFactory::PAYPAL);
        $this->assertEquals('fast_pay', PaymentMethodFactory::FAST_PAY);
    }
}
