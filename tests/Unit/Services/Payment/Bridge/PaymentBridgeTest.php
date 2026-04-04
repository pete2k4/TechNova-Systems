<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Payment\Bridge;

use App\Contracts\PaymentGatewayInterface;
use App\Services\Payment\Bridge\AbstractPaymentMethod;
use App\Services\Payment\CreditCardPayment;
use App\Services\Payment\Gateways\CreditCardGateway;
use App\Services\Payment\Gateways\FastPayGateway;
use App\Services\Payment\PayPalPayment;
use App\Services\Payment\Adapters\FastPayAdapter;
use PHPUnit\Framework\TestCase;

class PaymentBridgeTest extends TestCase
{
    /** @test */
    public function abstract_payment_method_delegates_to_gateway(): void
    {
        $gateway = new class implements PaymentGatewayInterface {
            public ?string $credential = null;
            public ?int $amountInCents = null;

            public function charge(string $credential, int $amountInCents): array
            {
                $this->credential = $credential;
                $this->amountInCents = $amountInCents;

                return [
                    'status' => 'approved',
                    'transaction_id' => 'test_123',
                ];
            }
        };

        $payment = new class($gateway, 'bridge-credential') extends AbstractPaymentMethod {
            public function getName(): string
            {
                return 'Bridge Test';
            }
        };

        $this->assertTrue($payment->process(12.34));
        $this->assertSame('bridge-credential', $gateway->credential);
        $this->assertSame(1234, $gateway->amountInCents);
    }

    /** @test */
    public function credit_card_payment_uses_credit_card_gateway(): void
    {
        $payment = new CreditCardPayment('4532015112830366');

        $this->assertTrue($payment->process(99.99));
        $this->assertSame('Credit Card', $payment->getName());
    }

    /** @test */
    public function paypal_payment_uses_paypal_gateway(): void
    {
        $payment = new PayPalPayment('user@example.com');

        $this->assertTrue($payment->process(49.99));
        $this->assertSame('PayPal', $payment->getName());
    }

    /** @test */
    public function fastpay_adapter_still_bridges_through_gateway(): void
    {
        $gateway = new class extends FastPayGateway {
            public ?string $capturedToken = null;
            public ?int $capturedAmount = null;

            public function charge(string $customerToken, int $amountInCents): array
            {
                $this->capturedToken = $customerToken;
                $this->capturedAmount = $amountInCents;

                return [
                    'status' => 'approved',
                    'transaction_id' => 'fp_bridge_123',
                ];
            }
        };

        $payment = new FastPayAdapter($gateway, 'fp_customer_12345678');

        $this->assertTrue($payment->process(19.95));
        $this->assertSame('fp_customer_12345678', $gateway->capturedToken);
        $this->assertSame(1995, $gateway->capturedAmount);
    }

    /** @test */
    public function gateways_are_directly_callable_implementors(): void
    {
        $creditCardGateway = new CreditCardGateway();
        $response = $creditCardGateway->charge('4532015112830366', 12345);

        $this->assertSame('approved', $response['status']);
        $this->assertStringStartsWith('cc_', $response['transaction_id']);
    }
}
